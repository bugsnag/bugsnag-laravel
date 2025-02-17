require_relative "../lib/laravel"
require_relative "../lib/utils"

PROJECT_ROOT = File.realpath("#{__dir__}/../../")
FIXTURE_PATH = File.realpath("#{PROJECT_ROOT}/features/fixtures/#{Laravel.fixture}")

Maze.hooks.before_all do
  # log to console, not a file
  Maze.config.file_log = false
  Maze.config.log_requests = true

  # don't wait so long for requests/not to receive requests locally
  unless ENV["CI"]
    Maze.config.receive_requests_wait = 10
    Maze.config.receive_no_requests_wait = 10
  end

  # bugsnag-laravel doesn't need to send the integrity header
  Maze.config.enforce_bugsnag_integrity = false

  # the laravel-latest fixture uses a different mechanism for installing the
  # bugsnag-laravel library (see 'setup-laravel-dev-fixture.sh')
  if Laravel.fixture != 'laravel-latest'
    if ENV["DEBUG"]
      puts "Installing bugsnag-laravel from '#{PROJECT_ROOT}' to '#{FIXTURE_PATH}'"
    end

    Utils.install_bugsnag(PROJECT_ROOT, FIXTURE_PATH)
  end
end

Maze.hooks.before do
  Maze::Runner.environment["BUGSNAG_API_KEY"] = $api_key
  Maze::Runner.environment["BUGSNAG_ENDPOINT"] = "http://#{Utils.current_ip}:#{Maze.config.port}/notify"
  Maze::Runner.environment["BUGSNAG_SESSION_ENDPOINT"] = "http://#{Utils.current_ip}:#{Maze.config.port}/sessions"
  Laravel.reset!
end

Before("@not-lumen") do
  skip_this_scenario if Laravel.lumen?
end

Before("@not-laravel") do
  skip_this_scenario unless Laravel.lumen?
end

Before("@requires-sessions") do
  skip_this_scenario unless Laravel.supports_sessions?
end

# add a '@not-X' tag for each fixture
fixtures = Dir.each_child(File.realpath("#{PROJECT_ROOT}/features/fixtures")) do |name|
  next unless name.match?(/^(laravel|lumen)/)

  Before("@not-#{name}") do
    skip_this_scenario if Laravel.fixture == name
  end
end
