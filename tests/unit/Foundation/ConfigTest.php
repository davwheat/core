<?php

/*
 * This file is part of Flarum.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace Flarum\Tests\unit\Foundation;

use Flarum\Foundation\Config;
use Flarum\Tests\unit\TestCase;
use InvalidArgumentException;
use RuntimeException;

class ConfigTest extends TestCase
{
    /** @test */
    public function it_complains_when_base_url_is_missing()
    {
        $this->expectException(InvalidArgumentException::class);

        new Config([]);
    }

    /** @test */
    public function it_wraps_base_url_in_value_object()
    {
        $config = new Config([
            'url' => 'https://flarum.local/myforum/',
        ]);

        $url = $config->url();
        $this->assertEquals('https', $url->getScheme());
        $this->assertEquals('/myforum', $url->getPath()); // Note that trailing slashes are removed
        $this->assertEquals('https://flarum.local/myforum', (string) $url);
    }

    /** @test */
    public function it_has_a_helper_for_debug_mode()
    {
        $config = new Config([
            'url' => 'https://flarum.local',
            'debug' => false,
        ]);

        $this->assertFalse($config->inDebugMode());

        $config = new Config([
            'url' => 'https://flarum.local',
            'debug' => true,
        ]);

        $this->assertTrue($config->inDebugMode());
    }

    /** @test */
    public function it_turns_off_debug_mode_by_default()
    {
        $config = new Config([
            'url' => 'https://flarum.local',
        ]);

        $this->assertFalse($config->inDebugMode());
    }

    /** @test */
    public function it_has_a_helper_for_maintenance_mode()
    {
        $config = new Config([
            'url' => 'https://flarum.local',
            'offline' => false,
        ]);

        $this->assertFalse($config->inMaintenanceMode());

        $config = new Config([
            'url' => 'https://flarum.local',
            'offline' => true,
        ]);

        $this->assertTrue($config->inMaintenanceMode());
    }

    /** @test */
    public function it_turns_off_maintenance_mode_by_default()
    {
        $config = new Config([
            'url' => 'https://flarum.local',
        ]);

        $this->assertFalse($config->inMaintenanceMode());
    }

    /** @test */
    public function it_exposes_additional_keys_via_array_access()
    {
        $config = new Config([
            'url' => 'https://flarum.local',
            'custom_a' => 'b',
        ]);

        $this->assertEquals('b', $config['custom_a']);
    }

    /** @test */
    public function it_exposes_nested_keys_via_dot_syntax()
    {
        $config = new Config([
            'url' => 'https://flarum.local',
            'nested' => [
                'first' => '1',
                'second' => '2',
            ],
        ]);

        $this->assertEquals('1', $config['nested.first']);
        $this->assertEquals('2', $config['nested.second']);
    }

    /** @test */
    public function it_does_not_allow_mutation_via_array_access()
    {
        $config = new Config([
            'url' => 'https://flarum.local',
            'custom_a' => 'b',
        ]);

        try {
            $config['custom_a'] = 'c';
        } catch (RuntimeException $_) {
        }

        // Ensure the value was not changed
        $this->assertEquals('b', $config['custom_a']);
    }

    /** @test */
    public function it_does_not_allow_removal_via_array_access()
    {
        $config = new Config([
            'url' => 'https://flarum.local',
            'custom_a' => 'b',
        ]);

        try {
            unset($config['custom_a']);
        } catch (RuntimeException $_) {
        }

        // Ensure the value was not changed
        $this->assertEquals('b', $config['custom_a']);
    }
}