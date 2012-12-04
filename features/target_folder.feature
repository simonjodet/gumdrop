Feature: target folder
  In order to have a simpler packaging/deploying setup
  As a Gumdrop user
  I want to be able to set the target folder as I want

  Scenario: Target folder is in the the source folder
    Given I have my test site "test_site"
    When I generate my site in a sub-folder "_site" of the source
    Then the site should be rendered correctly