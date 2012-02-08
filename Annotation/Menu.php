<?php

namespace Millwright\MenuBundle\Annotation;

/**
 * Represents a @Translate annotation.
 *
 * @Annotation
 * @Target("METHOD")
 * @author Stefan Zerkalica <zerkalica@gmail.com>
 */
final class Menu
{
    public $label;
    public $translateDomain;
    public $translateParams = array();

    public function __construct(array $values)
    {
        $values += array(
            'label'           => null,
            'translateDomain' => null,
            'translateParams' => array(),
        );
        $this->label  = $values['label'];
        $this->translateDomain = $values['translateDomain'];
        if ($values['translateParams']) {
            $this->translateParams = array_map('trim', explode(',', $values['translateParams']));
        }
    }
}
