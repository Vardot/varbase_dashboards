@varbase_dashboards @management
Feature: Varbase Dashboards - dashboard management
  As a site administrator
  I want to manage dashboards
  So that I can configure the admin dashboard layout

  Background:
    Given I am a logged in user with the "Webmaster" user

  Scenario: The dashboards administration list is available
    When I go to "/admin/structure/dashboard"
    Then I should see "Dashboard"

  Scenario: The default dashboard can be edited with Layout Builder
    When I go to "/admin/dashboard/dashboard"
    Then I should see "Dashboard"
