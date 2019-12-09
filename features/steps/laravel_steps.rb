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
  steps %{
    When I wait for the app to respond on port "#{getLaravelPort}"
  }
end

When("I navigate to the route {string}") do |route|
  steps %{
    When I navigate to the route "#{route}" on port "#{getLaravelPort}"
  }
end

Then("the exception {string} matches one of the following:") do |path, values|
  desired_value = read_key_path(find_request(get_request_index(nil))[:body], "events.0.exceptions.0.#{path}")
  assert_includes(values.raw.flatten, desired_value)
end

def getLaravelPort
  case ENV['LARAVEL_FIXTURE']
  when 'laravel66'
    61266
  when 'laravel58'
    61258
  else
    61256
  end
end