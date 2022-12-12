require_relative "../lib/laravel"
require_relative "../lib/utils"

Given(/^I enable session tracking$/) do
  steps %{
    Given I set environment variable "BUGSNAG_CAPTURE_SESSIONS" to "true"
  }
end

When(/^I start the laravel fixture$/) do
  steps %{
    When I start the service "#{Laravel.fixture}"
    And I wait for the host "localhost" to open port "#{Laravel.fixture_port}"
  }
end

module Maze
  class Docker
    class << self
      # TODO: remove when https://github.com/bugsnag/maze-runner/pull/425 is merged
      def exec(service, command, detach: false)
        flags = detach ? "--detach" : ""

        run_docker_compose_command("exec #{flags} #{service} #{command}")
      end

      # TODO: contribute this back to Maze Runner
      #       probably need a nicer API, capable of doing a copy in either
      #       direction (right now this can only copy from the service to the
      #       local machine)
      def cp(service, source:, destination:)
        run_docker_compose_command("cp #{service}:#{source} #{destination}")
      end
    end
  end
end

When("I start the laravel queue worker") do
  step("I start the laravel queue worker with --tries=1")
end

When("I start the laravel queue worker with --tries={int}") do |tries|
  Maze::Docker.exec(Laravel.fixture, Laravel.queue_worker_daemon_command(tries), detach: true)
end

When("I run the laravel queue worker") do
  step("I run the laravel queue worker with --tries=1")
end

When("I run the laravel queue worker with --tries={int}") do |tries|
  Maze::Docker.exec(Laravel.fixture, Laravel.queue_worker_once_command(tries))
end

When("I navigate to the route {string}") do |route|
  Laravel.navigate_to(route)
end

Then("the Laravel response matches {string}") do |regex|
  wait = Maze::Wait.new(timeout: 10)
  success = wait.until { Laravel.last_response != nil }

  raise 'No response from the Laravel fixture!' unless success

  assert_match(Regexp.new(regex), Laravel.last_response)
end

Then("the exception {string} matches one of the following:") do |path, values|
  body = Maze::Server.errors.current[:body]
  desired_value = Maze::Helper.read_key_path(body, "events.0.exceptions.0.#{path}")

  Maze.check.includes(values.raw.flatten, desired_value)
end

Then("the event {string} matches the current major Laravel version") do |path|
  # skip this assertion if we're running Lumen
  next if Laravel.lumen?

  # don't try to check the major version on the 'latest' fixture
  unless Laravel.latest?
    step("the event '#{path}' starts with '#{Laravel.major_version}'")
  end

  step("the event '#{path}' matches '^((\\d+\\.){2}\\d+|\\d\\.x-dev)$'")
end

Then("the session payload field {string} matches the current major Laravel version") do |path|
  # skip this assertion if we're running Lumen
  next if Laravel.lumen?

  # don't try to check the major version on the 'latest' fixture
  unless Laravel.latest?
    step("the session payload field '#{path}' starts with '#{Laravel.major_version}'")
  end

  step("the session payload field '#{path}' matches the regex '^((\\d+\\.){2}\\d+|\\d\\.x-dev)$'")
end

Then("the event {string} matches the current major Lumen version") do |path|
  # skip this assertion if we're running Laravel
  next unless Laravel.lumen?

  # don't try to check the major version on the 'latest' fixture
  unless Laravel.latest?
    step("the event '#{path}' starts with '#{Laravel.major_version}'")
  end

  step("the event '#{path}' matches '^((\\d+\\.){2}\\d+|\\d\\.x-dev)$'")
end

Then("the session payload field {string} matches the current major Lumen version") do |path|
  # skip this assertion if we're running Laravel
  next unless Laravel.lumen?

  # don't try to check the major version on the 'latest' fixture
  unless Laravel.latest?
    step("the session payload field '#{path}' starts with '#{Laravel.major_version}'")
  end

  step("the session payload field '#{path}' matches the regex '^((\\d+\\.){2}\\d+|\\d\\.x-dev)$'")
end

# TODO: remove when https://github.com/bugsnag/maze-runner/pull/433 is released
Then("the event has {int} breadcrumb(s)") do |expected|
  breadcrumbs = Maze::Server.errors.current[:body]['events'].first['breadcrumbs']

  Maze.check.equal(
    expected,
    breadcrumbs.length,
    "Expected event to have '#{expected}' breadcrumbs, but got: #{breadcrumbs}"
  )
end

Then("the event has no breadcrumbs") do
  breadcrumbs = Maze::Server.errors.current[:body]['events'].first['breadcrumbs']

  Maze.check.true(
    breadcrumbs.nil? || breadcrumbs.empty?,
    "Expected event not to have breadcrumbs, but got: #{breadcrumbs}"
  )
end

# conditionally run a step if the laravel version matches a specified version
#
# e.g. this will only check app.type on Laravel 5.2 and above:
#      on Laravel versions > 5.1 the event "app.type" equals "Queue"
Then(/^on Laravel versions (>=?|<=?|==) ([0-9.]+) (.*)$/) do |operator, version, step_to_run|
  should_run_step = Laravel.version.send(operator, version)

  # make sure this step is debuggable!
  $logger.debug("Laravel v#{Laravel.version} #{operator} #{version}? #{should_run_step}")

  if should_run_step
    step(step_to_run)
  else
    $logger.info("Skipping step on Laravel v#{Laravel.version}: #{step_to_run}")
  end
end
