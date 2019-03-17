Given(/^I enable session tracking$/) do
  steps %{
    When I set environment variable "BUGSNAG_CAPTURE_SESSIONS" to "true"
    And I set environment variable "BUGSNAG_SESSION_ENDPOINT" to "http://#{current_ip}:#{MOCK_API_PORT}"
  }
end

When(/^I start the laravel fixture$/) do
  laravel_fixture = ENV['LARAVEL_FIXTURE'] || 'laravel56'
  steps %{
    When I start the service "#{laravel_fixture}"
  }
end

When(/^I wait for the app to respond on the appropriate port$/) do
  laravel_port = ENV['LARAVEL_FIXTURE'] === 'laravel58' ? 61258 : 61256
  steps %{
    When I wait for the app to respond on port "#{laravel_port}"
  }
end

When("I navigate to the route {string}") do |route|
  laravel_port = ENV['LARAVEL_FIXTURE'] === 'laravel58' ? 61258 : 61256
  steps %{
    When I navigate to the route "#{route}" on port "#{laravel_port}"
  }
end