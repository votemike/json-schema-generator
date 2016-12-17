<?php namespace Votemike\JsonSchema;

use InvalidArgumentException;
use JsonSerializable;

class Schema implements JsonSerializable {

	private $title;

	private $description;

	private $type;

	private $allOf;

	private $anyOf;

	private $oneOf;

	private $minimum;

	private $properties;

	private $required;

	private $includeSchema;

	private $exclusiveMinimum;

	private $items;

	private $maxItems;

	private $minItems;

	private $uniqueItems;

	private $ref;

	private $definitions;

	private $format;

	private $patternProperties;

	private $additionalProperties;

	private $pattern;

	private $maximum;

	private $exclusiveMaximum;

	private $enum;

	public function __construct($includeSchema = false)
	{
		$this->includeSchema = $includeSchema;
	}

	/**
	 * @param Schema $schema
	 */
	public function addAllOf(Schema $schema)
	{
		$this->allOf[] = $schema;
	}

	/**
	 * @param Schema $schema
	 */
	public function addAnyOf(Schema $schema)
	{
		$this->anyOf[] = $schema;
	}

	/**
	 * @param string $key
	 * @param Schema $schema
	 */
	public function addDefinition($key, Schema $schema)
	{
		$this->definitions[$key] = $schema;
	}

	/**
	 * @param Schema $schema
	 */
	public function addOneOf(Schema $schema)
	{
		$this->oneOf[] = $schema;
	}

	/**
	 * @param string $regex Regex to match property name
	 * @param Schema $schema
	 * @throws InvalidArgumentException
	 */
	public function addPatternProperty($regex, Schema $schema)
	{
		if (@preg_match($regex, null) === false)
		{
			throw new InvalidArgumentException('Regex is invalid. Message: "' . error_get_last()['message'] . '"');
		}

		$this->patternProperties[$regex] = $schema;
	}

	/**
	 * @param bool|Schema $additionalProperties
	 */
	public function setAdditionalProperties($additionalProperties)
	{
		if (!(is_bool($additionalProperties) || $additionalProperties instanceof Schema))
		{
			throw new InvalidArgumentException('Parameter must be a bool or a Schema');
		}

		$this->additionalProperties = $additionalProperties;
	}

	/**
	 * @param string $key
	 * @param Schema $schema
	 * @param bool $required
	 */
	public function addProperty($key, Schema $schema, $required = false)
	{
		$this->properties[$key] = $schema;

		if ($required && (is_null($this->required) || !in_array($key, $this->required)))
		{
			$this->required[] = $key;
		}
	}

	/**
	 * @return array
	 */
	public function jsonSerialize()
	{
		$vars = array_filter(get_object_vars($this), function ($var)
		{
			return !is_null($var);
		});

		if ($vars['includeSchema'])
		{
			$vars = array_merge(['$schema' => "http://json-schema.org/draft-04/schema#"], $vars);
		}

		unset($vars['includeSchema']);

		if (isset($vars['ref']))
		{
			$vars['$ref'] = $vars['ref'];
			unset($vars['ref']);
		}

		return $vars;
	}

	/**
	 * @param string $description
	 */
	public function setDescription($description)
	{
		$this->description = $description;
	}

	/**
	 * @param array $enum
	 */
	public function setEnum(array $enum)
	{
		$this->enum = $enum;
	}

	/**
	 * @param string $format
	 */
	public function setFormat($format)
	{
		$this->format = $format;
	}

	/**
	 * @param Schema|Schema[] $items
	 * @param bool $uniqueItems
	 * @param int|null $minItems
	 * @param int|null $maxItems
	 */
	public function setItems($items, $uniqueItems = false, $minItems = null, $maxItems = null)
	{
		if ($minItems < 0)
		{
			throw new InvalidArgumentException('minItems must be greater than or equal to 0');
		}

		if ($maxItems < 0)
		{
			throw new InvalidArgumentException('maxItems must be greater than or equal to 0');
		}

		$this->items = $items;
		$this->uniqueItems = $uniqueItems;
		$this->minItems = $minItems;
		$this->maxItems = $maxItems;
	}

	/**
	 * @param int $maximum
	 * @param bool $exclusiveMaximum
	 */
	public function setMaximum($maximum, $exclusiveMaximum = false)
	{
		$this->maximum = $maximum;
		$this->exclusiveMaximum = $exclusiveMaximum;
	}

	/**
	 * @param int $minimum
	 * @param bool $exclusiveMinimum
	 */
	public function setMinimum($minimum, $exclusiveMinimum = false)
	{
		$this->minimum = $minimum;
		$this->exclusiveMinimum = $exclusiveMinimum;
	}

	/**
	 * @param string $pattern
	 */
	public function setPattern($pattern)
	{
		$this->pattern = $pattern;
	}

	/**
	 * @param string $ref
	 */
	public function setRef($ref)
	{
		$this->ref = $ref;
	}

	/**
	 * @param string $title
	 */
	public function setTitle($title)
	{
		$this->title = $title;
	}

	/**
	 * @param string|string[] $type
	 */
	public function setType($type)
	{
		if (is_array($type))
		{
			foreach ($type as $t)
			{
				$this->validateType($t);
			}
		}
		else
		{
			$this->validateType($type);
		}

		$this->type = $type;
	}

	/**
	 * @param bool $pretty
	 * @return string
	 */
	public function toJson($pretty = false)
	{
		$options = JSON_UNESCAPED_SLASHES;

		if ($pretty)
		{
			$options = $options | JSON_PRETTY_PRINT;
		}

		return json_encode($this, $options);
	}

	/**
	 * @param string $type
	 */
	private function validateType($type)
	{
		$allowedTypes = ['null', 'boolean', 'object', 'array', 'number', 'integer', 'string'];

		if (!in_array($type, $allowedTypes))
		{
			throw new InvalidArgumentException('Type must be null, boolean, object, array, number or string');
		}
	}
}
