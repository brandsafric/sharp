<?php

namespace Code16\Sharp\Tests\Unit\Form\Eloquent\Formatters;

use Code16\Sharp\Form\Eloquent\Formatters\UploadFormatter;
use Code16\Sharp\Form\Fields\SharpFormUploadField;
use Code16\Sharp\Tests\Fixtures\Person;
use Code16\Sharp\Tests\Unit\Form\Eloquent\SharpFormEloquentBaseTest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\File;

class UploadFormatterTest extends SharpFormEloquentBaseTest
{

    protected function setUp()
    {
        parent::setUp();

        config(['filesystems.disks.sharp_uploads' => [
            'driver' => 'local',
            'root' => storage_path('app/tmp'),
        ]]);

        config(['filesystems.disks.local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ]]);

        File::deleteDirectory(storage_path("app/tmp"));
    }

    /** @test */
    function we_can_format_value()
    {
        $fileName = $this->uploadedFile();
        $formatter = app(UploadFormatter::class);

        $formField = SharpFormUploadField::make("file")
            ->setStorageDisk("local")
            ->setStorageBasePath("data/Test");

        $this->assertEquals(
            "data/Test/$fileName",
            $formatter->format([
                "name" => $fileName,
                "uploaded" => true
            ], $formField, new Person)
        );
    }

    /** @test */
    function newly_uploaded_file_is_moved_to_the_configured_disk_and_directory()
    {
        $fileName = $this->uploadedFile();
        $formatter = app(UploadFormatter::class);

        $formField = SharpFormUploadField::make("file")
            ->setStorageDisk("local")
            ->setStorageBasePath("data/Test");

        $formatter->format([
            "name" => $fileName,
            "uploaded" => true
        ], $formField, new Person);

        $this->assertTrue(file_exists(storage_path("app/data/Test/$fileName")));
    }

    /** @test */
    function we_throw_an_exception_if_a_newly_uploaded_file_needs_a_modelId_in_path_with_a_non_existing_model()
    {
        $fileName = $this->uploadedFile();
        $formatter = app(UploadFormatter::class);

        $formField = SharpFormUploadField::make("file")
            ->setStorageDisk("local")
            ->setStorageBasePath("data/Test/{id}");

        $this->expectException(ModelNotFoundException::class);

        $formatter->format([
            "name" => $fileName,
            "uploaded" => true
        ], $formField, new Person());
    }

    /** @test */
    function we_substitute_parameters_with_model_attributes_in_storage_base_path()
    {
        $fileName = $this->uploadedFile();
        $formatter = app(UploadFormatter::class);

        $formField = SharpFormUploadField::make("file")
            ->setStorageDisk("local")
            ->setStorageBasePath("data/Test/{id}/{name}");

        $person = Person::create([
            "name" => "john"
        ]);

        $this->assertEquals(
            "data/Test/{$person->id}/{$person->name}/$fileName",
            $formatter->format([
                "name" => $fileName,
                "uploaded" => true
            ], $formField, $person)
        );
    }

    private function uploadedFile()
    {
        return basename(
            (new \Illuminate\Http\Testing\FileFactory)->image("image.jpg", 600, 600)->store("tmp")
        );
    }
}