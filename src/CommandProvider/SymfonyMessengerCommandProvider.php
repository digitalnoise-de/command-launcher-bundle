<?php
declare(strict_types=1);

namespace Digitalnoise\CommandLauncherBundle\CommandProvider;

use Digitalnoise\CommandLauncher\CommandProvider;
use ReflectionClass;
use ReflectionException;
use RuntimeException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

final class SymfonyMessengerCommandProvider implements CommandProvider
{
    public function __construct(private readonly string $rootDir)
    {
    }

    /**
     * @throws ReflectionException
     */
    public function all(): array
    {
        $finder = new Finder();
        $finder->files()->path('.php')->in($this->rootDir);

        $messages = [];

        foreach ($finder as $item) {
            try {
                $rc = $this->reflectionClass($item);
            } catch ( ReflectionException|RuntimeException $exception ) {
                continue;
            }

            $attributes = $rc->getAttributes();

            if (count($attributes) === 0) {
                continue;
            }

            foreach ($attributes as $attribute) {
                if ($attribute->getName() !== AsMessageHandler::class) {
                    continue;
                }

                $invoke = null;

                try {
                    $invoke = $rc->getMethod('__invoke');
                } catch ( ReflectionException $exception ) {
                    continue;
                }

                $parameters = $invoke->getParameters();

                if (count($parameters) === 0) {
                    continue;
                }

                $parameterType = $parameters[0]->getType();

                if ($parameterType->isBuiltin()) {
                    continue;
                }

                $messages[] = $parameterType->getName();
            }
        }

        return $messages;
    }

    private function extractNamespace(string $content): string
    {
        $namespaceMatches = [];
        preg_match('/namespace (.+);/', $content, $namespaceMatches);

        if (count($namespaceMatches) === 0 || !isset($namespaceMatches[1])) {
            throw new RuntimeException('No namespace found');
        }

        return $namespaceMatches[1];
    }

    private function extractClass(string $content): string
    {
        $classMatches = [];
        preg_match('/class (\w+)/', $content, $classMatches);

        if (count($classMatches) === 0 || !isset($classMatches[1])) {
            throw new RuntimeException('No namespace found');
        }

        return $classMatches[1];
    }

    /**
     * @throws ReflectionException
     */
    private function reflectionClass(mixed $item): ReflectionClass
    {
        $content = file_get_contents($item->getPathname());

        if (!$content) {
            throw new RuntimeException(sprintf('Error reading "%s".', $item->getPathname()));
        }

        $namespaceMatches = $this->extractNamespace($content);
        $classMatches     = $this->extractClass($content);

        $path = sprintf('%s\%s', $namespaceMatches, $classMatches);

        return new ReflectionClass($path);
    }
}
