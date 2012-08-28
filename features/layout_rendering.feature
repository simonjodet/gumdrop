Feature: layout rendering
  In order to create nice-looking websites
  As a Gumdrop user
  I need to have a Twig layout rendered with the Markdown page inside them

  Scenario: No _layout folder but a layout conf
    Given I have my test site "test_site"
    And It does not have a "_layout" folder
    When I generate my site
    Then the site should be rendered correctly

