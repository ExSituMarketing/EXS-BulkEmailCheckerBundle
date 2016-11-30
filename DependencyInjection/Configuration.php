<?php

namespace EXS\BulkEmailCheckerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('exs_bulk_email_checker');

        $rootNode
            ->children()
                ->scalarNode('enabled')
                    ->defaultValue(true)
                ->end()
                ->scalarNode('pass_on_error')
                    ->defaultValue(true)
                ->end()
                ->scalarNode('check_mx')
                    ->defaultValue(false)
                ->end()
                ->scalarNode('api_key')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('api_url')
                    ->defaultValue('http://api-v4.bulkemailchecker2.com/?key=#api_key#&email=#email#')
                ->end()
                ->arrayNode('whitelisted_domains')
                    ->prototype('scalar')->end()
                ->end()
                ->arrayNode('blacklisted_domains')
                    ->prototype('scalar')->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
