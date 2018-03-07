<?php

namespace Dyrynda\Artisan\BulkImport\Handlers;

use SplFileInfo;
use SplFileObject;
use Dyrynda\Artisan\Exceptions\ImportFileException;

class Csv extends Base
{
    public function __construct(SplFileInfo $file)
    {
        parent::__construct($file);

        $this->fileHandle->setFlags(SplFileObject::READ_CSV);
    }

    public function getData()
    {
        $this->fileHandle->rewind();

        $data = [];

        $fields = $this->getFields();

        foreach ($this->fileHandle as $index => $row) {
            // skip header
            if ($index != 0) {
                $data[] = array_combine($fields, $row);
            }
        }

        return $data;
    }

    protected function validateSyntax()
    {
        $this->fileHandle->rewind();

        $fields = array_filter(array_map('trim', explode(',', $this->fileHandle->current())));

        foreach ($this->fileHandle as $row) {
            if (count($fields) != count(explode(',', $row))) {
                throw ImportFileException::invalidSyntax($this->file->getFilename());
            }
        }
    }

    /**
     * Get list of columns from the file.
     *
     * @return array
     */
    protected function getFields()
    {
        $this->fileHandle->rewind();

        $fields = array_filter(array_map('strtolower', array_map('trim', $this->fileHandle->current())));

        return $fields;
    }
}