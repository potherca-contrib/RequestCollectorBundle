<?php

namespace Deuzu\RequestCollectorBundle\Collector;

use Deuzu\RequestCollectorBundle\Entity\RequestObject;
use Monolog\Handler\StreamHandler;
use Psr\Log\LoggerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class LoggerCollector.
 *
 * @author Florian Touya <florian.touya@gmail.com>
 */
class LoggerCollector implements CollectorInterface
{
    /** @var LoggerInterface */
    private $logger;

    /** @var LoggerInterface */
    private $deprecatedLogger;

    /** @var SerializerInterface */
    private $serializer;

    /** @var string */
    private $logFolder;

    /** @var string */
    private $kernelEnvironment;

    /**
     * @param SerializerInterface $serializer
     * @param LoggerInterface     $logger
     * @param LoggerInterface     $deprecatedLogger deprecated
     * @param string              $logFolder deprecated
     * @param string              $kernelEnvironment deprecated
     */
    public function __construct(
        SerializerInterface $serializer,
        LoggerInterface $logger,
        LoggerInterface $deprecatedLogger, // deprecated
        $logFolder, // deprecated
        $kernelEnvironment // deprecated
    ) {
        $this->serializer = $serializer;
        $this->logger = $logger;
        $this->deprecatedLogger = $deprecatedLogger; // deprecated
        $this->logFolder = $logFolder; // deprecated
        $this->kernelEnvironment = $kernelEnvironment; // deprecated
    }

    /**
     * {@inheritdoc}
     */
    public function collect(RequestObject $requestObject, array $parameters = [])
    {
        if (!empty($parameters['logHandlers'])) {
            $handlers = $this->logger->getHandlers();

            foreach ($handlers as $handler) {
                dump(get_class_methods($handler));die();
                // if (in_array()) {
                //
                // }
            }

            $this->logger->setHandlers($parameters['logHandlers']);
            $this->logger->info('request_collector.collect', $this->serializer->normalize($requestObject));
            dump('fdsfs');die();
        }

        // deprecated
        if ($parameters['logFile']) {
            $parameters = $this->resolveCollectorParameters($parameters);
            $logFile = sprintf('%s/%s.%s', $this->logFolder, $this->kernelEnvironment, $parameters['logFile']);

            $this->deprecatedLogger->pushHandler(new StreamHandler($logFile));
            $this->deprecatedLogger->info('request_collector.collect', $this->serializer->normalize($requestObject));
        }
    }

    /**
     * @deprecated
     *
     * @param array $parameters
     *
     * @return array
     */
    private function resolveCollectorParameters(array $parameters = [])
    {
        $resolver = new OptionsResolver();
        $resolver->setRequired(['logFile']);

        return $resolver->resolve($parameters);
    }
}
