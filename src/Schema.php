<?php namespace Votemike\JsonSchema;

use InvalidArgumentException;
use JsonSerializable;

class Schema implements JsonSerializable {

	private $description;

	private $title;

	private $type;

	private $minimum;

	private $properties;

	private $required;

	private $includeSchema;

	private $exclusiveMinimum;

	private $items;

	private $minItems;

	private $uniqueItems;

	private $ref;

	private $definitions;

	private $format;

	private $patternProperties;

	public function __construct($includeSchema = false)
	{
		$this->includeSchema = $includeSchema;
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
	 * @param bool $exclusiveMinimum
	 */
	public function setExclusiveMinimum($exclusiveMinimum)
	{
		$this->exclusiveMinimum = $exclusiveMinimum;
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
	 */
	public function setItems($items)
	{
		$this->items = $items;
	}

	/**
	 * @param int $minimum
	 */
	public function setMinimum($minimum)
	{
		$this->minimum = $minimum;
	}

	/**
	 * @param int $minItems
	 */
	public function setMinItems($minItems)
	{
		$this->minItems = $minItems;
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
	 * @param bool $uniqueItems
	 */
	public function setUniqueItems($uniqueItems)
	{
		$this->uniqueItems = $uniqueItems;
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
