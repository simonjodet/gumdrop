Feature: static files
  In order to create nice-looking websites
  As a Gumdrop user
  I need static files to be copied over the destination folder

  Scenario: My CSS file is moved
    Given I have my test site "test_site"
    And It has a CSS file at "style/default.css"
    When I generate my site
    Then I should have the CSS file in the destination folder

  Scenario: Markdown files are ignored
    Given I have my test site "test_site"
    And It has a Markdown file at "testFile2.md"
    When I generate my site
    Then I should not have the Markdown file in the destination folder

  Scenario: Layout files are ignored
    Given I have my test site "test_site"
    And It has a layout folder
    When I generate my site
    Then I should not have the Layout folder in the destination folder

#  Scenario: Folders starting with "_" are ignored
#    Given I have my test site "test_site"
#    And It has a static folder starting with "_"
#    When I generate my site
#    Then I should not have the folder starting with "_" in the destination folder
