<?php

namespace Millwright\MenuBundle\Annotation;

/**
 * Represents a @Translate annotation.
 *
 * @Annotation
 * @Target("CLASS")
 * @author Stefan Zerkalica <zerkalica@gmail.com>
 */
final class MenuDefault
{
    public $translateDomain;
    public $translateParameters;

    public $showNonAuthorized;
    public $showAsText;

    public function __construct(array $values)
    {
        foreach($values as $property => $value) {
            $this->$property = $value;
        }

        if ($this->translateParameters) {
            $this->translateParameters = array_map('trim', explode(',', $this->translateParameters));
        }
    }
}
