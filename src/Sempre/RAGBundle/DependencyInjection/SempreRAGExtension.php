<?php
namespace Sempre\RAGBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class SempreRAGExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('rag.openai_api_key', $config['openai_api_key']);
        $container->setParameter('rag.mistral_api_key', $config['mistral_api_key']);
        $container->setParameter('rag.embedding_provider', $config['embedding_provider']);
    }
}
