@varbase_dashboards @dashboard
Feature: Varbase Dashboards - the admin dashboard
  As a site administrator
  I want the Varbase Dashboards admin dashboard
  So that I get an at-a-glance overview of the site

  Background:
    Given I am a logged in user with the "Webmaster" user

  Scenario: The admin dashboard page loads
    When I go to "/admin/dashboard"
    Then I should see "Dashboard"

  Scenario: The default Varbase dashboard renders its widgets
    When I go to "/admin/dashboard"
    Then I should see "Add content"
    And I should see "My Site Overview"
    And I should see "Content"
