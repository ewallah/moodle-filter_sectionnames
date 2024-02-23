@filter @filter_sectionnames
Feature: Show sectionnames filter functionality
  To write rich text - I need to render sectionnames in content.

  Background:
    Given the following "courses" exist:
      | shortname | fullname |
      | C1        | Course 1 |
    And the following "users" exist:
      | username |
      | student1 |
    And the following "course enrolments" exist:
      | user     | course | role    |
      | student1 | C1     | student |
    And the following "activities" exist:
      | activity | name         | course | content  | contentformat |
      | page     | PageName1    | C1     | number 1 | 1             |
      | page     | PageName2    | C1     | number 1 | 1             |
      | page     | PageName3    | C1     | number 1 | 1             |
    And the "sectionnames" filter is "on"
    And the "activitynames" filter is "off"
    And the "tex" filter is "off"
    And the "algebra" filter is "off"
    And the "displayh5p" filter is "off"
    And the "emoticon" filter is "off"
    And the "mediaplugin" filter is "off"
    And the "multilang" filter is "off"
    And the "mathjaxloader" filter is "off"
    And the "glossary" filter is "off"
    And the "data" filter is "off"
    And the "emailprotect" filter is "off"
    And I log in as "admin"
    And I am on "Course 1" course homepage with editing mode on
    And I edit the section "1" and I fill the form with:
      | Custom                     | 1        |
      | New value for Section name | number 1 |
    And I am on "Course 1" course homepage
    And I edit the section "2" and I fill the form with:
      | Custom                     | 1        |
      | New value for Section name | number 2 |
    And I am on "Course 1" course homepage
    And I edit the section "3" and I fill the form with:
      | Custom                     | 1        |
      | New value for Section name | number 3 |
    And I log out

  Scenario: Check if the automatic section links were created.
    Given I log in as "student1"
    And I am on "Course 1" course homepage
    Then I should not see "Topic 1"
    And I should see "number 1"
    And I follow "PageName1"
    Then I should see "number 1"
    And I follow "number 1"
    And I follow "PageName2"
    Then I should see "number 1"
    And I follow "number 1"
    Then I should see "number 1"
    And I follow "PageName2"
    And I follow "number 1"
    Then I should see "PageName1"
