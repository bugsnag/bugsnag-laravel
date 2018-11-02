Given(/^I enable session tracking$/) do
  steps %{
    When I set environment variable "BUGSNAG_CAPTURE_SESSIONS" to "true"
    And I set environment variable "BUGSNAG_SESSION_ENDPOINT" to "http://#{current_ip}:#{MOCK_API_PORT}"
  }
end