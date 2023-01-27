# digitalnoise CommandLauncherBundle

---

[![Total Downloads](https://img.shields.io/packagist/dt/digitalnoise/command-launcher-bundle.svg)](https://packagist.org/packages/digitalnoise/command-launcher-bundle)
[![MIT License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](https://packagist.org/packages/digitalnoise/command-launcher-bundle)

This bundle provides functionality to execute any message (or command) of an application which has messenger based structure, using the Symfony CLI. An example for such applications would be one, that makes use of the [symfony/messenger](https://github.com/symfony/messenger) or something similar (as long as you use a structure, that passes a message to a bus which calls a separate handler, it works).

### What is this bundle for?

Imagine a REST-Controller which passes a message to the injected messenger.

```php
// ...
class ActivateUserController
{
    // ...
    public function __invoke(Request $request): Response
    {
        $userId = UserId::fromRequest($request);
        $message = new ActivateUserMessage(UserId $id)
        
        $this->messageBus->dispatch($message);
        
        // ...
    }
}
```

This is a pretty simple example, but this will lead us to the point. Let's assume, that this route cannot be used for some abstract reason, but you need to activate a user immediately. Of course, you can write a small cli command and execute it afterwards. But there could be much more complex cases. Those could make it useful, to have the possibility, to pass those messages directly to the message bus.

This bundle provides the possibility to execute those commands from the cli without writing separate commands for each case.

Simply by calling:

    bin/console digitalnoise:command:launch

This will provide a list of commands/messages.

    [ActivateUser   ] Acme\Message\ActivateUser
    [DeleteUser     ] Acme\Message\DeleteUser
    [CreateSomething] Acme\Message\CreateSomething
    > ActivateUser

After choosing an option (e.g., ActivateUser), you can pass the required parameter to the message. The list will look as the list above, just with your provided user ids with the related information (it's up to you, how they'll look like).

## Setup

---

### Installation

Using this package is similar to all Symfony bundles. The following steps must be performed

1. Download the bundle
2. Enable the bundle

### Step 1: Download the bundle

Open a command console, enter your project directory, and execute the following command to download the latest stable version of this bundle and add it as a dependency to your project:

    composer require digitalnoise/command-launcher-bundle

### Step 2: Enable the bundle

Then, enable the bundle by adding new Digitalnoise\CommandLauncherBundle\CommandLauncherBundle() to the bundles array of the registerBundles function in your project's app/AppKernel.php file:

```php
<?php

// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...

            new Digitalnoise\CommandLauncherBundle\CommandLauncherBundle(),
        );

        // ...
    }

    // ...
}
```

## Using the bundle

---

You need to take care for the following interfaces and classes, to make this bundle work:

    Digitalnoise\CommandLauncher\CommandProvider
    Digitalnoise\CommandLauncher\CommandLauncher
    Digitalnoise\CommandLauncher\ParameterResolver

    Digitalnoise\CommandLauncher\ParameterOption

### 1. The CommandProvider interface

The function `all()` will gather all commands inside your application which will be passed to the message bus. Since any application has its own structure and messenger service, you need to implement this function individually.
```php
/**
 * @return list<class-string>
 */
public function all(): array;
```

### 2. The CommandLauncher interface

This interface is responsible for the execution of a message. The implentation will take a message bus as class parameter. The `launch(object $command)` function will receive a message and pass it to the used bus inside your application.
```php
public function launch(object $command): void;
```

### 3. The ParameterResolver interface

```php
/**
 * @param class-string $class
 */
public function supports(string $class): bool;

/**
 * @param class-string $class
 *
 * @return list<ParameterOption>
 */
public function options(string $class): array;

public function value(string $key): mixed;
```

Let's call the upper example with the user activation to mind. Here, we **don't pass a string** to the message, but rather an **object of the class UserId**.

By implementing `supports(string $class)`, you provide the information, for which type of parameter, this resolver shall be called. In case of the **UserId** class, it would look as follows:

```php
public function supports(string $class): bool
{
    return $class === UserId::class;
}
```

As you want to choose, which user will be activated, you need to provide options. The following code shows an example, how the implementation could look like.

All user objects will be loaded and transformed to a list of `ParameterOption` objects.

```php
public function options(string $class): array
{
    $users = $this->userRepository->all();

    return array_map(fn(User $user) => new ParameterOption($user->getEmail(), $user->getFullName()), $users);
}
```

Finally, you need to define the actual value, which will be returned by choosing a user option, when using the command.

```php
public function value(string $key): mixed
{
    // object of type UserId
    return $this->userRepository->findByEmail($key)->id();
}
```

Let's ignore the fact, that the user will be fetched inspite of being so some seconds before. It's your choice, how you want to hold the data inside your `UserIdProvider` class. This is the value, which is actually passed to the message, when it's built.

## Bring it all together and use the command

---

Your `services.yml` could look like this. In this example, we assume, that the [thephpleague/tactician](https://github.com/thephpleague/tactician) command bus is used.

```yml
# We pass all command handlers with the tag tactician.handler to the command provider
Acme\Command\CommandLauncher\CommandProvider:
    arguments:
        $handlers: !tagged_iterator tactician.handler

Digitalnoise\CommandLauncher\CommandProvider:
    alias: 'Acme\Command\CommandLauncher\CommandProvider'

Acme\Command\CommandLauncher\CommandLauncher:

Digitalnoise\CommandLauncher\CommandLauncher:
    alias: 'Acme\Command\CommandLauncher\CommandLauncher'

Acme\Command\CommandLauncher\Resolvers\UserIdResolver:
Acme\Command\CommandLauncher\Resolvers\MacrotrendIdResolver:

Digitalnoise\CommandLauncherBundle\Command\CommandLauncherCommand:
    arguments:
        $parameterResolvers:
            - '@Acme\Command\CommandLauncher\Resolvers\UserIdResolver'
```

With the configuration above, we should have brought together the necessary classes and information to execute the command by typing

    bin/console digitalnoise:command:launch
