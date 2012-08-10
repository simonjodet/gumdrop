Feature: static files
  In order to create nice-looking websites
  As a Gumdrop
  I need static files to be copied over the destination folder

  Scenario: My CSS file is moved
    Given I have my test site "test_site"
    And It has a CSS file at "style/default.css"
    When I generate my site in "destination_site"
    Then I should have the CSS file in the destination folder
    And Then delete the destination folder