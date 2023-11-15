<?php
namespace Maven\ProjectPractice\UnitTest\Commands;

use Maven\ProjectPractice\Blog\Commands\Arguments;
use Maven\ProjectPractice\Blog\Exceptions\ArgumentsException;
use Maven\ProjectPractice\Blog\Exceptions\InvalidArgumentException;
use Maven\ProjectPractice\Blog\UUID;
use PHPUnit\Framework\TestCase;

class ArgumentsTest extends TestCase{
    public static function argumentsProvider(): iterable
    {
        return [
            ['some_string', 'some_string'],
            [' some_string', 'some_string'],
            [' some_string ', 'some_string'],
            [123, '123'],
            [12.3, '12.3'],
        ];
    }

    public function testItThrowsAnExceptionWhenArgumentIsAbsent(): void
    {
        $arguments = new Arguments([]);
        $this->expectException(ArgumentsException::class);
        $arguments->get('some_key');
    }

    /**
     * @dataProvider argumentsProvider
     */
    public function testItConvertArgumentsToStrings($inputValue, $expectedValue): void
    {
        $arguments = new Arguments(['some_key' => $inputValue]);
        $value = $arguments->get('some_key');

        $this->assertSame($expectedValue, $value);
    }


    public function testFromArgvSkipsInvalidArguments(): void
    {
        $argv = ['--username=testuser', '--first_name=test', '--last_name=user', 'invalid_argument'];
        $arguments = Arguments::fromArgv($argv);
        $this->expectException(ArgumentsException::class);
        $arguments->get('invalid_argument');
    }

    public function testConstructorSkipsEmptyValues(): void
    {
        $arguments = new Arguments(['some_key' => 'some_value', 'empty_key' => '']);
        $this->expectException(ArgumentsException::class);
        $arguments->get('empty_key');
    }

    public function testGetUuidReturnsValidUuid(): void
    {
        $uuidString = UUID::random();
        $this->assertEquals($uuidString, $uuidString->getUuid());
    }
    public function testGetUuidThrowsExceptionForInvalidUuid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $uuid = new UUID('11-22-33');
    }


}