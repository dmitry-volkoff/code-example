<?php declare(strict_types=1);
namespace CRM\Utils;
//require_once(__DIR__ . '/Template.class.php');

interface UIInterface
{
	public function __construct(string $template_file);
	public function render(object &$template_DTO) : void;
	public function render_no_argument(&$template_DTO) : void;
	public function message(string $msg, string $error) : array;
	public function total(int $changed_contacts, int $found_companies) : void;
	public function hr_line() : void;
	public function close_output() : void;
}
