<?php

namespace Xaraya\Modules\Webhooks\Configuration;

use Exception;
use Throwable;

/**
 * Webhooks module configuration
 */
class WebhooksConfig
{
    /** @var array<string, mixed> */
    protected array $config;

    public function __construct()
    {
        $this->loadConfig();
    }

    public function loadConfig(): void
    {
        $filepath = dirname(__DIR__, 5) . '/html/var/cache/api/webhooks_config.php';
        try {
            $this->config = require $filepath;
        } catch (Throwable) {
            //file_put_contents($filepath, "<?php\n\nreturn [];\n");
        }
        if (empty($this->config)) {
            $this->config = $this->getDefaultConfig();
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function getDefaultConfig(): array
    {
        return require dirname(__DIR__, 2) . '/xardata/webhooks_config.php';
    }

    /**
     * @return array<string, mixed>
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * @param array<string, mixed> $config
     * @return void
     */
    public function setConfig(array $config = []): void
    {
        $this->config = $config;
    }

    /**
     * @return void
     */
    public function saveConfig(string $filepath): void
    {
        $output = "<?php\n\n\$config = " . var_export($this->config, true) . ";\n";
        $output .= "return \$config;\n";
        file_put_contents($filepath, $output);
    }

    /**
     * @uses \sys::autoload()
     * @return object
     */
    public function getEndpoint(string $type = '', string $name = ''): object
    {
        $type = $type ?: 'webhook';
        $name = $name ?: 'home';
        if ($type === 'passthru') {
            // map original uri to framework uri: from /passthru/symfony/... to /...
            $mapping = [
                '/' . $type . '/' . $name . '/' => '/',
            ];
            if (!empty($mapping)) {
                $_SERVER['REQUEST_URI'] = str_replace(array_keys($mapping), array_values($mapping), $_SERVER['REQUEST_URI']);
                if (!empty($_SERVER['PATH_INFO'])) {
                    $_SERVER['PATH_INFO'] = str_replace(array_keys($mapping), array_values($mapping), $_SERVER['PATH_INFO']);
                }
            }
            // pass through to other framework
        }
        if (empty($this->config[$name])) {
            throw new Exception('Invalid entrypoint for ' . htmlspecialchars($name));
        }
        if (empty($this->config[$name]['enabled'])) {
            throw new Exception('Disabled entrypoint for ' . htmlspecialchars($name));
        }
        return new $this->config[$name]['endpoint']($this->config[$name]);
    }

    /**
     * @return list<string>
     */
    public function listWebhooks(): array
    {
        $webhooks = [];
        foreach ($this->config as $name => $values) {
            if (empty($values['enabled'])) {
                continue;
            }
            if ($values['type'] == 'passthru') {
                $webhooks[] = $values['type'] . '/' . $values['name'] . '/';
            } else {
                $webhooks[] = $values['type'] . '/' . $values['name'];
            }
        }
        return $webhooks;
    }
}
