<?php declare(strict_types=1);
namespace CRM\Utils;

interface TemplateFactory
{
	public function createTemplate(): TemplateInterface;
}
