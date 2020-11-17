require 'net/http'

Given(/^I enable session tracking$/) do
  steps %{
    When I set environment variable "BUGSNAG_CAPTURE_SESSIONS" to "true"
    And I set environment variable "BUGSNAG_SESSION_ENDPOINT" to "http://#{current_ip}:9339"
  }
end

Given('I configure the bugsnag endpoint') do
  steps %{
    Given I set environment variable "BUGSNAG_ENDPOINT" to "http://#{current_ip}:9339"
  }
end

When(/^I start the laravel fixture$/) do
  steps %{
    When I start the service "#{fixture}"
    And I wait for the host "localhost" to open port "#{fixture_port}"
  }
end

When("I navigate to the route {string}") do |route|
  navigate_to(route)
end

Then("the exception {string} matches one of the following:") do |path, values|
  desired_value = read_key_path(Server.current_request[:body], "events.0.exceptions.0.#{path}")
  assert_includes(values.raw.flatten, desired_value)
end

def fixture
  ENV['LARAVEL_FIXTURE'] || 'laravel56'
end

def fixture_port
  case fixture
  when 'laravel-latest' then 61299
  when 'laravel66' then 61266
  when 'laravel58' then 61258
  when 'laravel56' then 61256
  else raise "Unknown laravel fixture '#{ENV['LARAVEL_FIXTURE']}'!"
  end
end

def current_ip
  return 'host.docker.internal' if OS.mac?

  ip_addr = `ifconfig | grep -Eo 'inet (addr:)?([0-9]*\\\.){3}[0-9]*' | grep -v '127.0.0.1'`
  ip_list = /((?:[0-9]*\.){3}[0-9]*)/.match(ip_addr)
  ip_list.captures.first
end

def navigate_to(route, attempts = 0)
  Net::HTTP.get('localhost', route, fixture_port)
rescue => e
  raise "Failed to navigate to #{route} (#{e})" if attempts > 15

  sleep 1
  navigate_to(route, attempts + 1)
end
