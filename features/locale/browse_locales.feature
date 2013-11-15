@javascript
Feature: Browse locales
  In order to check wether or not a locale is available is the catalog
  As a user
  I need to be able to see active and inactive locales in the catalog

  Background:
    Given the "default" catalog configuration
    And the following locales:
      | code  | fallback | activated |
      | de_DE |          | no        |
      | en_US |          | yes       |
      | fr_FR | en_US    | yes       |
    And I am logged in as "admin"

  Scenario: Successfully display locales
    Given I am on the locales page
    Then I should see the columns Code and Activated
    When I filter by "Activated" with value "yes"
    Then the grid should contain 2 elements
    And I should see activated locales en_US and fr_FR
