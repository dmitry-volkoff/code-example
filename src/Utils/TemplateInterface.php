<?php declare(strict_types=1);
namespace CRM\Utils;

interface TemplateInterface
{
	public function render(object &$template_DTO) : void;
	public function render_buffer(object &$template_DTO) : string;
}
