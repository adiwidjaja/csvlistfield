Usage:

    static $db = array(
        "RssUrls" => "Text",
    );

    static $casting = array(
        "RssUrls" => "CsvListValue('SourceTitle', 'Url')"
    );

    $fields->addFieldToTab("Editing.Properties",
            new CsvListField("RssUrls", "URLs", new FieldSet(
                new TextField("SourceTitle", "Titel"),
                new TextField("Url", "URL")
            ), "", "CsvListField_lines"));
