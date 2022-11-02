<?php
/**
 * Mage Tracer.
 *
 * @category   Magetracer
 * @package    Magetracer_StoreOptimization
 * @author     Magetracer
 * @copyright  Copyright (c) Mage Tracer (https://www.magetracer.com)
 * @license    https://www.magetracer.com/license.html
 */

namespace Magetracer\StoreOptimization\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command class.
 */
class ImageCompress extends Command
{
    const TYPE_ARGUMENT = 'type';
    /**
     * @var \Magetracer\StoreOptimization\Model\Converter\Adapter
     */
    protected $imageAdapter;

    /**
     * @var Magento\Framework\Filesystem\Io\File
     */
    protected $file;

    /**
     * @var \Magento\Framework\Filesystem\Driver\File
     */
    protected $fileDriver;

    /**
     *
     * @param \Magetracer\StoreOptimization\Model\Converter\Adapter $imageAdapter
     * @param \Magento\Framework\Filesystem\Io\File $file
     * @param \Magento\Framework\Filesystem\Driver\File $fileDriver
     */
    public function __construct(
        \Magetracer\StoreOptimization\Model\Converter\Adapter $imageAdapter,
        \Magento\Framework\Filesystem\Io\File $file,
        \Magento\Framework\Filesystem\Driver\File $fileDriver
    ) {
        parent::__construct();
        $this->imageAdapter = $imageAdapter;
        $this->file = $file;
        $this->fileDriver = $fileDriver;
    }

    protected function configure()
    {
        $this->setName('image:compress');
        $this->setDescription('image:compress');
        $this->setDefinition([
            new InputArgument(
                self::TYPE_ARGUMENT,
                InputArgument::OPTIONAL,
                'compression type jpeg or webp'
            ),
            new InputOption(
                "path",
                '-p',
                InputArgument::OPTIONAL,
                'absolute path of the folder or image where image(s) need to be compressed'
            ),

        ]);
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $type = $input->getArgument(self::TYPE_ARGUMENT);
        $path = $input->getOption("path");
        
        if ($type == 'webp' || $type == 'jpeg') {
            
            if ($this->fileDriver->isDirectory($path)) {
                
                /**
                 * @phpcs:disable
                 */
                $files = $this->dirToOptions($path);
				// echo "<pre>";
				// print_r($files);
				
				
                /**
                 * @phpcs:enable
                 */
                foreach ($files as $file) {
                   // $filePath = $path.DIRECTORY_SEPARATOR.$file;
                    $destinationPath = $this->getDestinationPath($file, $type);
                    if ($this->fileDriver->isFile($file)) {
                        $this->imageAdapter->convert(
                            $file,
                            $destinationPath,
                            [],
                            $type
                        );
                        $output->writeln("<info>converted image: ".$destinationPath." </info>");
                    }
                }
            } elseif ($this->fileDriver->isFile($path)) {
                $destinationPath = $this->getDestinationPath($path, $type);
                $this->imageAdapter->convert(
                    $path,
                    $destinationPath,
                    [],
                    $type
                );
                $output->writeln("<info>converted image: ".$destinationPath." </info>");
            } else {
                throw new \InvalidArgumentException('path is invalid');
            }
        } else {
            throw new \InvalidArgumentException('invalid compression type provided correct values are webp or jpeg');
        }
    }
	
	public function dirToOptions($path = __DIR__, &$out = [],$level = 0) {
		$items = scandir($path);
		foreach($items as $item) {
			// ignore items strating with a dot (= hidden or nav)
			if (strpos($item, '.') === 0) {
				continue;
			}

			$fullPath = $path . DIRECTORY_SEPARATOR . $item;
			// add some whitespace to better mimic the file structure
			$item = str_repeat('&nbsp;', $level * 3) . $item;
			// file
			if (is_file($fullPath)) {
				 $file_extension = explode('.',$item);
				$file_extension = strtolower(end($file_extension));
				
				$webpfile = str_replace(".".$file_extension,".webp",$fullPath);

				if(($file_extension = "jpeg" || $file_extension = "png" || $file_extension = "jpg") && !is_file($webpfile))
				{
				$out[] = $fullPath;
				}
			}
			// dir
			else if (is_dir($fullPath)) {
				
				$this->dirToOptions($fullPath, $out, $level + 1);
			}
		}
		return $out;
	}
	public function scanDirAndSubdir($dir, &$out = []) {
		$sun = scandir($dir);

		foreach ($sun as $a => $filename) {
			$way = realpath($dir . DIRECTORY_SEPARATOR . $filename);
			if (!is_dir($way)) {
				$out[] = $way;
			} else if ($filename != "." && $filename != "..") {
				$this->scanDirAndSubdir($way, $out);
				$out[] = $way;
			}
		}

		return $out;
	}

    /**
     * get destinatio path
     *
     * @param string $path
     * @param string $type
     * @return string
     */
    public function getDestinationPath($path, $type)
    {
        $imagePathParts = $this->file->getPathInfo($path);
        return $imagePathParts['dirname']. DIRECTORY_SEPARATOR .$imagePathParts['filename'] .
        '.'.$type;
    }
}
