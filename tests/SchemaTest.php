<?php namespace Votemike\JsonSchema\Tests;

use InvalidArgumentException;
use PHPUnit_Framework_TestCase;
use TypeError;
use Votemike\JsonSchema\Schema;

class SchemaTest extends PHPUnit_Framework_TestCase {

	public function typeDataProvider()
	{
		return [
			'null' => ['null'],
			'boolean' => ['boolean'],
			'object' => ['object'],
			'array' => ['array'],
			'number' => ['number'],
			'integer' => ['integer'],
			'string' => ['string'],
		];
	}

	/**
	 * @dataProvider typeDataProvider
	 * @param string $type
	 */
	public function testSetType($type)
	{
		$jsonSchema = '{"type":"' . $type . '"}';

		$schema = new Schema();
		$schema->setType($type);
		$this->assertEquals($jsonSchema, $schema->toJson());
	}

	public function testSetTypeWithArray()
	{
		$jsonSchema = '{"type":["null","boolean","object","array","number","integer","string"]}';

		$schema = new Schema();
		$schema->setType(['null', 'boolean', 'object', 'array', 'number', 'integer', 'string']);
		$this->assertEquals($jsonSchema, $schema->toJson());
	}

	public function testSetTypeWithInvalidType()
	{
		$schema = new Schema();
		$this->expectException(InvalidArgumentException::class);
		$schema->setType("invalid");
	}

	public function testSetTypeWithArrayWithInvalidType()
	{
		$schema = new Schema();
		$this->expectException(InvalidArgumentException::class);
		$schema->setType(["invalid"]);
	}

	public function testSetDescription()
	{
		// From http://json-schema.org/examples.html
		$jsonSchema = '{"description":"Age in years","type":"integer"}';

		$schema = new Schema();
		$schema->setType("integer");
		$schema->setDescription("Age in years");
		$this->assertEquals($jsonSchema, $schema->toJson());
	}

	public function testSetMinimum()
	{
		// From http://json-schema.org/examples.html
		$jsonSchema = '{"description":"Age in years","type":"integer","minimum":0,"exclusiveMinimum":false}';

		$schema = new Schema();
		$schema->setType("integer");
		$schema->setDescription("Age in years");
		$schema->setMinimum(0);
		$this->assertEquals($jsonSchema, $schema->toJson());
	}

	public function testSetTitle()
	{
		// From http://json-schema.org/examples.html
		$jsonSchema = '{"title":"Example Schema"}';

		$schema = new Schema();
		$schema->setTitle("Example Schema");
		$this->assertEquals($jsonSchema, $schema->toJson());
	}

	public function testAddProperty()
	{
		// From http://json-schema.org/examples.html
		$jsonSchema = '{"title":"Example Schema","type":"object","properties":{"firstName":{"type":"string"}}}';

		$firstName = new Schema();
		$firstName->setType("string");

		$schema = new Schema();
		$schema->setTitle("Example Schema");
		$schema->setType("object");
		$schema->addProperty("firstName", $firstName);
		$this->assertEquals($jsonSchema, $schema->toJson());
	}

	public function testAddRequiredProperty()
	{
		// From http://json-schema.org/examples.html
		$jsonSchema = '{"title":"Example Schema","type":"object","properties":{"firstName":{"type":"string"}},"required":["firstName"]}';

		$firstName = new Schema();
		$firstName->setType("string");

		$schema = new Schema();
		$schema->setTitle("Example Schema");
		$schema->setType("object");
		$schema->addProperty("firstName", $firstName, true);
		$this->assertEquals($jsonSchema, $schema->toJson());
	}

	public function testBasicExample()
	{
		// From http://json-schema.org/examples.html
		$jsonSchema = '{"title":"Example Schema","type":"object","properties":{"firstName":{"type":"string"},"lastName":{"type":"string"},"age":{"description":"Age in years","type":"integer","minimum":0,"exclusiveMinimum":false}},"required":["firstName","lastName"]}';

		$firstName = new Schema();
		$firstName->setType("string");

		$lastName = new Schema();
		$lastName->setType("string");

		$age = new Schema();
		$age->setType("integer");
		$age->setDescription("Age in years");
		$age->setMinimum(0);

		$schema = new Schema();
		$schema->setTitle("Example Schema");
		$schema->setType("object");
		$schema->addProperty("firstName", $firstName, true);
		$schema->addProperty("lastName", $lastName, true);
		$schema->addProperty("age", $age);
		$this->assertEquals($jsonSchema, $schema->toJson());
	}

	public function testIncludingSchema()
	{
		// From http://json-schema.org/example1.html
		$jsonSchema = '{"$schema":"http://json-schema.org/draft-04/schema#"}';

		$schema = new Schema(true);
		$this->assertEquals($jsonSchema, $schema->toJson());
	}

	public function testSetExclusiveMinimum()
	{
		$jsonSchema = '{"minimum":0,"exclusiveMinimum":true}';

		$schema = new Schema();
		$schema->setMinimum(0, true);
		$this->assertEquals($jsonSchema, $schema->toJson());
	}

	public function testSetItems()
	{
		$jsonSchema = '{"items":{"type":"string"},"uniqueItems":false}';

		$item = new Schema();
		$item->setType("string");

		$schema = new Schema();
		$schema->setItems($item);
		$this->assertEquals($jsonSchema, $schema->toJson());
	}

	public function testSetUniqueItems()
	{
		$jsonSchema = '{"items":{"type":"string"},"uniqueItems":true}';
		$item = new Schema();
		$item->setType("string");

		$schema = new Schema();
		$schema->setItems($item, true);
		$this->assertEquals($jsonSchema, $schema->toJson());
	}

	public function testSetMinItems()
	{
		$jsonSchema = '{"items":{"type":"string"},"minItems":2,"uniqueItems":false}';
		$item = new Schema();
		$item->setType("string");

		$schema = new Schema();
		$schema->setItems($item, false, 2);
		$this->assertEquals($jsonSchema, $schema->toJson());
	}

	public function testSetMinItemsWithValueLessThanZero()
	{
		$item = new Schema();
		$item->setType("string");

		$schema = new Schema();
		$this->expectException(InvalidArgumentException::class);
		$schema->setItems($item, false, -2);
	}

	public function testSetMaxItems()
	{
		$jsonSchema = '{"items":{"type":"string"},"maxItems":2,"uniqueItems":false}';
		$item = new Schema();
		$item->setType("string");

		$schema = new Schema();
		$schema->setItems($item, false, null, 2);
		$this->assertEquals($jsonSchema, $schema->toJson());
	}

	public function testSetMaxItemsWithValueLessThanZero()
	{
		$item = new Schema();
		$item->setType("string");

		$schema = new Schema();
		$this->expectException(InvalidArgumentException::class);
		$schema->setItems($item, false, 2, -2);
	}

	public function testSetRef()
	{
		// From http://json-schema.org/example1.html
		$jsonSchema = '{"$ref":"http://json-schema.org/geo"}';

		$schema = new Schema();
		$schema->setRef("http://json-schema.org/geo");
		$this->assertEquals($jsonSchema, $schema->toJson());
	}

	public function testExample1()
	{
		// From http://json-schema.org/example1.html
		$jsonSchema = '{"$schema":"http://json-schema.org/draft-04/schema#","title":"Product set","type":"array","items":{"title":"Product","type":"object","properties":{"id":{"description":"The unique identifier for a product","type":"number"},"name":{"type":"string"},"price":{"type":"number","minimum":0,"exclusiveMinimum":true},"tags":{"type":"array","items":{"type":"string"},"minItems":1,"uniqueItems":true},"dimensions":{"type":"object","properties":{"length":{"type":"number"},"width":{"type":"number"},"height":{"type":"number"}},"required":["length","width","height"]},"warehouseLocation":{"description":"Coordinates of the warehouse with the product","$ref":"http://json-schema.org/geo"}},"required":["id","name","price"]},"uniqueItems":false}';

		$id = new Schema();
		$id->setDescription("The unique identifier for a product");
		$id->setType("number");

		$name = new Schema();
		$name->setType("string");

		$price = new Schema();
		$price->setType("number");
		$price->setMinimum(0, true);

		$item = new Schema();
		$item->setType('string');

		$tags = new Schema();
		$tags->setType("array");
		$tags->setItems($item, true, 1);

		$length = new Schema();
		$length->setType("number");

		$width = new Schema();
		$width->setType("number");

		$height = new Schema();
		$height->setType("number");

		$dimensions = new Schema();
		$dimensions->setType("object");
		$dimensions->addProperty("length", $length, true);
		$dimensions->addProperty("width", $width, true);
		$dimensions->addProperty("height", $height, true);

		$warehouseLocation = new Schema();
		$warehouseLocation->setDescription("Coordinates of the warehouse with the product");
		$warehouseLocation->setRef("http://json-schema.org/geo");

		$items = new Schema();
		$items->setTitle("Product");
		$items->setType("object");
		$items->addProperty("id", $id, true);
		$items->addProperty("name", $name, true);
		$items->addProperty("price", $price, true);
		$items->addProperty("tags", $tags);
		$items->addProperty("dimensions", $dimensions);
		$items->addProperty("warehouseLocation", $warehouseLocation);

		$schema = new Schema(true);
		$schema->setTitle("Product set");
		$schema->setType("array");
		$schema->setItems($items);
		$this->assertEquals($jsonSchema, $schema->toJson());
	}

	public function testAddDefinition()
	{
		$jsonSchema = '{"type":"object","definitions":{"aDefinition":{"type":"object"}}}';

		$definition = new Schema();
		$definition->setType("object");

		$schema = new Schema();
		$schema->setType("object");
		$schema->addDefinition("aDefinition", $definition);
		$this->assertEquals($jsonSchema, $schema->toJson());
	}

	public function testSetFormat()
	{
		$jsonSchema = '{"format":"email"}';

		$schema = new Schema();
		$schema->setFormat('email');
		$this->assertEquals($jsonSchema, $schema->toJson());
	}

	public function testAddPatternProperty()
	{
		$jsonSchema = '{"patternProperties":{"/[A-Z]{3}/":{"type":"number"}}}';

		$property = new Schema();
		$property->setType('number');

		$schema = new Schema();
		$schema->addPatternProperty('/[A-Z]{3}/', $property);
		$this->assertEquals($jsonSchema, $schema->toJson());
	}

	public function testAddPatternPropertyWithInvalidRegexThrowsException()
	{
		$schema = new Schema();
		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessageRegExp('/(Regex is invalid\. Message: ").*(No ending delimiter \'\/\' found)/');
		$schema->addPatternProperty('/[A-Z]{3}', new Schema());
	}

	public function testAddAllOf()
	{
		$jsonSchema = '{"allOf":[{"type":"number"},{"minimum":0,"exclusiveMinimum":false}]}';

		$schemaA = new Schema();
		$schemaA->setType('number');

		$schemaB = new Schema();
		$schemaB->setMinimum(0);

		$schema = new Schema();
		$schema->addAllOf($schemaA);
		$schema->addAllOf($schemaB);
		$this->assertEquals($jsonSchema, $schema->toJson());
	}

	public function testAddAnyOf()
	{
		$jsonSchema = '{"anyOf":[{"type":"number"},{"type":"boolean"}]}';

		$schemaA = new Schema();
		$schemaA->setType('number');

		$schemaB = new Schema();
		$schemaB->setType('boolean');

		$schema = new Schema();
		$schema->addAnyOf($schemaA);
		$schema->addAnyOf($schemaB);
		$this->assertEquals($jsonSchema, $schema->toJson());
	}

	public function testAddOneOf()
	{
		$jsonSchema = '{"oneOf":[{"type":"number"},{"type":"boolean"}]}';

		$schemaA = new Schema();
		$schemaA->setType('number');

		$schemaB = new Schema();
		$schemaB->setType('boolean');

		$schema = new Schema();
		$schema->addOneOf($schemaA);
		$schema->addOneOf($schemaB);
		$this->assertEquals($jsonSchema, $schema->toJson());
	}

	public function testSetAdditionalPropertiesWithBool()
	{
		$jsonSchema = '{"additionalProperties":false}';

		$schema = new Schema();
		$schema->setAdditionalProperties(false);
		$this->assertEquals($jsonSchema, $schema->toJson());
	}

	public function testSetAdditionalPropertiesWithSchema()
	{
		$jsonSchema = '{"additionalProperties":{"type":"string"}}';

		$item = new Schema();
		$item->setType("string");

		$schema = new Schema();
		$schema->setAdditionalProperties($item);
		$this->assertEquals($jsonSchema, $schema->toJson());
	}

	public function testSetAdditionalPropertiesWithStringThrowsException()
	{
		$schema = new Schema();
		$this->expectException(InvalidArgumentException::class);
		$schema->setAdditionalProperties('string');
	}

	public function testSetPattern()
	{
		$jsonSchema = '{"pattern":"/[A-Z]{3}/"}';

		$schema = new Schema();
		$schema->setPattern('/[A-Z]{3}/');
		$this->assertEquals($jsonSchema, $schema->toJson());
	}

	public function testSetMaximum()
	{
		$jsonSchema = '{"maximum":-42,"exclusiveMaximum":false}';

		$schema = new Schema();
		$schema->setMaximum(-42);
		$this->assertEquals($jsonSchema, $schema->toJson());
	}

	public function testSetExclusiveMaximum()
	{
		$jsonSchema = '{"maximum":42,"exclusiveMaximum":true}';

		$schema = new Schema();
		$schema->setMaximum(42, true);
		$this->assertEquals($jsonSchema, $schema->toJson());
	}

	public function testSetEnum()
	{
		$jsonSchema = '{"enum":["Something",null]}';

		$schema = new Schema();
		$schema->setEnum(['Something', null]);
		$this->assertEquals($jsonSchema, $schema->toJson());
	}

	public function testSetEnumWithNonArrayThrowsException()
	{
		$schema = new Schema();
		$this->expectException(TypeError::class);
		$schema->setEnum('string');
	}

	public function testExample2()
	{
		// From http://json-schema.org/example2.html
		$jsonSchema = '{
    "$schema": "http://json-schema.org/draft-04/schema#",
    "description": "schema for an fstab entry",
    "type": "object",
    "required": [ "storage" ],
    "properties": {
        "storage": {
            "type": "object",
            "oneOf": [
                { "$ref": "#/definitions/diskDevice" },
                { "$ref": "#/definitions/diskUUID" },
                { "$ref": "#/definitions/nfs" },
                { "$ref": "#/definitions/tmpfs" }
            ]
        },
        "fstype": {
            "enum": [ "ext3", "ext4", "btrfs" ]
        },
        "options": {
            "type": "array",
            "minItems": 1,
            "items": { "type": "string" },
            "uniqueItems": true
        },
        "readonly": { "type": "boolean" }
    },
    "definitions": {
        "diskDevice": {
            "properties": {
                "type": { "enum": [ "disk" ] },
                "device": {
                    "type": "string",
                    "pattern": "^/dev/[^/]+(/[^/]+)*$"
                }
            },
            "required": [ "type", "device" ],
            "additionalProperties": false
        },
        "diskUUID": {
            "properties": {
                "type": { "enum": [ "disk" ] },
                "label": {
                    "type": "string",
                    "pattern": "^[a-fA-F0-9]{8}-[a-fA-F0-9]{4}-[a-fA-F0-9]{4}-[a-fA-F0-9]{4}-[a-fA-F0-9]{12}$"
                }
            },
            "required": [ "type", "label" ],
            "additionalProperties": false
        },
        "nfs": {
            "properties": {
                "type": { "enum": [ "nfs" ] },
                "remotePath": {
                    "type": "string",
                    "pattern": "^(/[^/]+)+$"
                },
                "server": {
                    "type": "string",
                    "oneOf": [
                        { "format": "host-name" },
                        { "format": "ipv4" },
                        { "format": "ipv6" }
                    ]
                }
            },
            "required": [ "type", "remotePath", "server"],
            "additionalProperties": false
        },
        "tmpfs": {
            "properties": {
                "type": { "enum": [ "tmpfs" ] },
                "sizeInMB": {
                    "type": "integer",
                    "minimum": 16,
                    "maximum": 512,
                    "exclusiveMinimum": false,
                    "exclusiveMaximum": false
                }
            },
            "required": [ "type", "sizeInMB" ],
            "additionalProperties": false
        }
    }
}';

		$refOne = new Schema();
		$refOne->setRef('#/definitions/diskDevice');

		$refTwo = new Schema();
		$refTwo->setRef('#/definitions/diskUUID');

		$refThree = new Schema();
		$refThree->setRef('#/definitions/nfs');

		$refFour = new Schema();
		$refFour->setRef('#/definitions/tmpfs');

		$storage = new Schema();
		$storage->setType("object");
		$storage->addOneOf($refOne);
		$storage->addOneOf($refTwo);
		$storage->addOneOf($refThree);
		$storage->addOneOf($refFour);

		$fstype = new Schema();
		$fstype->setEnum(["ext3", "ext4", "btrfs"]);

		$item = new Schema();
		$item->setType('string');

		$options = new Schema();
		$options->setType("array");
		$options->setItems($item, true, 1);

		$readonly = new Schema();
		$readonly->setType('boolean');

		$type = new Schema();
		$type->setEnum(['disk']);

		$device = new Schema();
		$device->setType('string');
		$device->setPattern("^/dev/[^/]+(/[^/]+)*$");

		$diskDevice = new Schema();
		$diskDevice->addProperty('type', $type, true);
		$diskDevice->addProperty('device', $device, true);
		$diskDevice->setAdditionalProperties(false);

		$label = new Schema();
		$label->setType('string');
		$label->setPattern("^[a-fA-F0-9]{8}-[a-fA-F0-9]{4}-[a-fA-F0-9]{4}-[a-fA-F0-9]{4}-[a-fA-F0-9]{12}$");

		$diskUUID = new Schema();
		$diskUUID->addProperty('type', $type, true);
		$diskUUID->addProperty('label', $label, true);
		$diskUUID->setAdditionalProperties(false);

		$type = new Schema();
		$type->setEnum(['nfs']);

		$remotePath = new Schema();
		$remotePath->setType('string');
		$remotePath->setPattern('^(/[^/]+)+$');

		$hostName = new Schema();
		$hostName->setFormat('host-name');

		$ipv4 = new Schema();
		$ipv4->setFormat('ipv4');

		$ipv6 = new Schema();
		$ipv6->setFormat('ipv6');

		$server = new Schema();
		$server->setType('string');
		$server->addOneOf($hostName);
		$server->addOneOf($ipv4);
		$server->addOneOf($ipv6);

		$nfs = new Schema();
		$nfs->addProperty('type', $type, true);
		$nfs->addProperty('remotePath', $remotePath, true);
		$nfs->addProperty('server', $server, true);
		$nfs->setAdditionalProperties(false);

		$type = new Schema();
		$type->setEnum(['tmpfs']);

		$sizeInMB = new Schema();
		$sizeInMB->setType('integer');
		$sizeInMB->setMinimum(16);
		$sizeInMB->setMaximum(512);

		$tmpfs = new Schema();
		$tmpfs->addProperty('type', $type, true);
		$tmpfs->addProperty('sizeInMB', $sizeInMB, true);
		$tmpfs->setAdditionalProperties(false);

		$schema = new Schema(true);
		$schema->setDescription("schema for an fstab entry");
		$schema->setType("object");
		$schema->addProperty("storage", $storage, true);
		$schema->addProperty("fstype", $fstype);
		$schema->addProperty("options", $options);
		$schema->addProperty("readonly", $readonly);
		$schema->addDefinition("diskDevice", $diskDevice);
		$schema->addDefinition("diskUUID", $diskUUID);
		$schema->addDefinition("nfs", $nfs);
		$schema->addDefinition("tmpfs", $tmpfs);
		$this->assertJsonStringEqualsJsonString($jsonSchema, $schema->toJson());
	}

	public function testSetNot()
	{
		$jsonSchema = '{"not":{"type":"string"}}';

		$item = new Schema();
		$item->setType("string");

		$schema = new Schema();
		$schema->setNot($item);
		$this->assertEquals($jsonSchema, $schema->toJson());
	}

	public function testSetMinLength()
	{
		$jsonSchema = '{"minLength":37}';

		$schema = new Schema();
		$schema->setMinLength(37);
		$this->assertEquals($jsonSchema, $schema->toJson());
	}

	public function testSetMinLengthLessThanZeroThrowsException()
	{
		$schema = new Schema();
		$this->expectException(InvalidArgumentException::class);
		$schema->setMinLength(-37);
	}

	public function testSetMaxLength()
	{
		$jsonSchema = '{"maxLength":37}';

		$schema = new Schema();
		$schema->setMaxLength(37);
		$this->assertEquals($jsonSchema, $schema->toJson());
	}

	public function testSetMaxLengthLessThanZeroThrowsException()
	{
		$schema = new Schema();
		$this->expectException(InvalidArgumentException::class);
		$schema->setMaxLength(-37);
	}
}
