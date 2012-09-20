Feature: Markdown files
  In order to avoid messing around with HTML
  As a Gumdrop user
  I want Markdown files to be converted to HTML

  Scenario: My Markdown file is converted
    Given I have my test site "test_site"
    And It has a Markdown file at "testFile2.md"
    When I generate my site
    Then I should have the HTML version created
