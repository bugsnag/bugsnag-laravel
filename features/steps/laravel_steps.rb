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

# TODO: contribute this back to Maze Runner
#       https://github.com/bugsnag/maze-runner/pull/425
module Maze
  class Docker
    class << self
      def exec(service, command, detach: false)
        flags = detach ? "--detach" : ""

        run_docker_compose_command("exec #{flags} #{service} #{command}")
      end

      def cp(service, source:, destination:)
        # cp only exists in 'docker compose' not 'docker-compose', i.e. only
        # with a space and not a hyphen
        # TODO: can we run all docker compose commands with a space?
        run_docker_space_compose_command("cp #{service}:#{source} #{destination}")
      end

      private

      def get_docker_space_compose_command(command)
        project_name = compose_project_name.nil? ? '' : "-p #{compose_project_name}"

        "docker compose #{project_name} -f #{COMPOSE_FILENAME} #{command}"
      end

      def run_docker_space_compose_command(command, success_codes: nil)
        command = get_docker_space_compose_command(command)

        @last_command_logs, @last_exit_code = Runner.run_command(command, success_codes: success_codes)
      end
    end
  end
end

When("I start the laravel queue worker") do
  step("I start the laravel queue worker with --tries=1")
end

When("I start the laravel queue worker with --tries={int}") do |tries|
  Maze::Docker.exec(Laravel.fixture, Laravel.queue_worker_command(tries: tries), detach: true)
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
