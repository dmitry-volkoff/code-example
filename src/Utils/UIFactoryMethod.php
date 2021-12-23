<?php declare(strict_types=1);
namespace CRM\Utils;

/**
 * Class: UIFactoryMethod
 *
 * @see UIFactory
 */
class UIFactoryMethod implements UIFactoryInterface
{
	private $template_file;
	
	/**
	 * __construct
	 *
	 *
	 * @param string $template_file
	 *
	 * @return
	 */
	public function __construct(string $template_file)
	{
		$this->template_file = $template_file;
	}
	
	/**
	 * createUI
	 *
	 *
	 *
	 * @return UI
	 */
	public function createUI(): UIInterface
	{
		if (PHP_SAPI === 'cli')
		{
			return new UICli($this->template_file);
		}
		else
		{
			return new UIHtml($this->template_file);
		}
	}
}
