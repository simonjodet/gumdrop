@travis
Feature: Standalone site
  In order to simplify my website setup
  As a Gumdrop user
  I want Gumdrop to be declarable as  a Composer dependency for my website

  Scenario: I can create a standalone site
    Given I have my test site "test_site_standalone"
    And I install the dependencies of my standalone test site with Composer
    When I generate my site with the installed dependency
    Then the site should be rendered without layout