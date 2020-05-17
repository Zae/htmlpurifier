<?php

declare(strict_types=1);

namespace HTMLPurifier\DefinitionCache;

use HTMLPurifier\Definition;
use HTMLPurifier\Config;
use HTMLPurifier\DefinitionCache;
use HTMLPurifier\Exception;

/**
 * Class HTMLPurifier\DefinitionCache\HTMLPurifier_DefinitionCache_Serializer
 */
class Serializer extends DefinitionCache
{
    /**
     * @param Definition $def
     * @param Config     $config
     *
     * @return int|bool
     */
    public function add(Definition $def, Config $config)
    {
        if (!$this->checkDefType($def)) {
            return false;
        }

        $file = $this->generateFilePath($config);
        if (file_exists($file)) {
            return false;
        }

        if (!$this->prepareDir($config)) {
            return false;
        }

        return $this->write($file, serialize($def), $config);
    }

    /**
     * @param Definition $def
     * @param Config     $config
     *
     * @return int|bool
     */
    public function set(Definition $def, Config $config)
    {
        if (!$this->checkDefType($def)) {
            return false;
        }

        $file = $this->generateFilePath($config);
        if (!$this->prepareDir($config)) {
            return false;
        }

        return $this->write($file, serialize($def), $config);
    }

    /**
     * @param Definition $def
     * @param Config     $config
     *
     * @return int|bool
     */
    public function replace(Definition $def, Config $config)
    {
        if (!$this->checkDefType($def)) {
            return false;
        }

        $file = $this->generateFilePath($config);
        if (!file_exists($file)) {
            return false;
        }

        if (!$this->prepareDir($config)) {
            return false;
        }

        return $this->write($file, serialize($def), $config);
    }

    /**
     * @param Config $config
     *
     * @return bool|Config
     */
    public function get(Config $config)
    {
        $file = $this->generateFilePath($config);
        if (!file_exists($file)) {
            return false;
        }

        return unserialize(file_get_contents($file));
    }

    /**
     * @param Config $config
     *
     * @return bool
     */
    public function remove(Config $config): bool
    {
        $file = $this->generateFilePath($config);
        if (!file_exists($file)) {
            return false;
        }

        return unlink($file);
    }

    /**
     * @param Config $config
     *
     * @return bool
     */
    public function flush(Config $config): bool
    {
        if (!$this->prepareDir($config)) {
            return false;
        }

        $dir = $this->generateDirectoryPath($config);
        $dh = opendir($dir);
        // Apparently, on some versions of PHP, readdir will return
        // an empty string if you pass an invalid argument to readdir.
        // So you need this test.  See #49.
        if ($dh === false) {
            return false;
        }

        while (($filename = readdir($dh)) !== false) {
            if (empty($filename)) {
                continue;
            }

            if ($filename[0] === '.') {
                continue;
            }

            unlink($dir . '/' . $filename);
        }
        closedir($dh);

        return true;
    }

    /**
     * @param Config $config
     *
     * @return bool
     * @throws \HTMLPurifier\Exception
     */
    public function cleanup(Config $config): bool
    {
        if (!$this->prepareDir($config)) {
            return false;
        }

        $dir = $this->generateDirectoryPath($config);
        $dh = opendir($dir);
        // See #49 (and above).
        if ($dh === false) {
            return false;
        }

        while (($filename = readdir($dh)) !== false) {
            if (empty($filename)) {
                continue;
            }

            if ($filename[0] === '.') {
                continue;
            }

            $key = substr($filename, 0, -4);
            if ($this->isOld($key, $config)) {
                unlink($dir . '/' . $filename);
            }
        }
        closedir($dh);

        return true;
    }

    /**
     * Generates the file path to the serial file corresponding to
     * the configuration and definition name
     *
     * @param Config $config
     *
     * @return string
     * @throws Exception
     * @todo Make protected
     */
    public function generateFilePath(Config $config): string
    {
        $key = $this->generateKey($config);

        return $this->generateDirectoryPath($config) . '/' . $key . '.ser';
    }

    /**
     * Generates the path to the directory contain this cache's serial files
     *
     * @param Config $config
     *
     * @return string
     * @note No trailing slash
     * @throws Exception
     * @todo Make protected
     */
    public function generateDirectoryPath(Config $config): string
    {
        $base = $this->generateBaseDirectoryPath($config);

        return $base . '/' . $this->type;
    }

    /**
     * Generates path to base directory that contains all definition type
     * serials
     *
     * @param Config $config
     *
     * @return mixed|string
     * @throws \HTMLPurifier\Exception
     * @todo Make protected
     */
    public function generateBaseDirectoryPath(Config $config)
    {
        $base = $config->get('Cache.SerializerPath');
        $base = \is_null($base) ? HTMLPURIFIER_PREFIX . '/HTMLPurifier/DefinitionCache/Serializer' : $base;

        return $base;
    }

    /**
     * Convenience wrapper function for file_put_contents
     *
     * @param string $file File name to write to
     * @param string $data Data to write into file
     * @param Config $config
     *
     * @return int|bool Number of bytes written if success, or false if failure.
     * @throws \HTMLPurifier\Exception
     */
    private function write(string $file, string $data, Config $config)
    {
        $result = file_put_contents($file, $data);
        if ($result !== false) {
            // set permissions of the new file (no execute)
            $chmod = $config->get('Cache.SerializerPermissions');
            if ($chmod !== null) {
                chmod($file, $chmod & 0666);
            }
        }

        return $result;
    }

    /**
     * Prepares the directory that this type stores the serials in
     *
     * @param Config $config
     *
     * @return bool True if successful
     * @throws Exception
     */
    private function prepareDir(Config $config): bool
    {
        $directory = $this->generateDirectoryPath($config);
        $chmod = $config->get('Cache.SerializerPermissions');
        if ($chmod === null) {
            if (!@mkdir($directory) && !is_dir($directory)) {
                trigger_error(
                    'Could not create directory ' . $directory . '',
                    E_USER_WARNING
                );

                return false;
            }

            return true;
        }

        if (!is_dir($directory)) {
            $base = $this->generateBaseDirectoryPath($config);
            if (!is_dir($base)) {
                trigger_error(
                    'Base directory ' . $base . ' does not exist,
                    please create or change using %Cache.SerializerPath',
                    E_USER_WARNING
                );

                return false;
            }

            if (!$this->testPermissions($base, $chmod)) {
                return false;
            }

            if (!@mkdir($directory, $chmod) && !is_dir($directory)) {
                trigger_error(
                    'Could not create directory ' . $directory . '',
                    E_USER_WARNING
                );

                return false;
            }

            if (!$this->testPermissions($directory, $chmod)) {
                return false;
            }
        } elseif (!$this->testPermissions($directory, $chmod)) {
            return false;
        }

        return true;
    }

    /**
     * Tests permissions on a directory and throws out friendly
     * error messages and attempts to chmod it itself if possible
     *
     * @param string    $dir   Directory path
     * @param int|null  $chmod Permissions
     *
     * @return bool True if directory is writable
     */
    private function testPermissions(string $dir, ?int $chmod): bool
    {
        // early abort, if it is writable, everything is hunky-dory
        if (is_writable($dir)) {
            return true;
        }

        if (!is_dir($dir)) {
            // generally, you'll want to handle this beforehand
            // so a more specific error message can be given
            trigger_error(
                'Directory ' . $dir . ' does not exist',
                E_USER_WARNING
            );

            return false;
        }

        if (\function_exists('posix_getuid') && $chmod !== null) {
            // POSIX system, we can give more specific advice
            if (fileowner($dir) === posix_getuid()) {
                // we can chmod it ourselves
                $chmod |= 0700;
                if (chmod($dir, $chmod)) {
                    return true;
                }
            } elseif (filegroup($dir) === posix_getgid()) {
                $chmod |= 0070;
            } else {
                // PHP's probably running as nobody, so we'll
                // need to give global permissions
                $chmod |= 0777;
            }

            trigger_error(
                'Directory ' . $dir . ' not writable, ' .
                'please chmod to ' . decoct($chmod),
                E_USER_WARNING
            );
        } else {
            // generic error message
            trigger_error(
                'Directory ' . $dir . ' not writable, ' .
                'please alter file permissions',
                E_USER_WARNING
            );
        }

        return false;
    }
}
