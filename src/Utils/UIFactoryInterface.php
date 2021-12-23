<?php declare(strict_types=1);
namespace CRM\Utils;

interface UIFactoryInterface
{
	public function __construct(string $template_file);
	public function createUI(): UIInterface;
}
