require_relative "../lib/laravel"
require_relative "../lib/utils"

PROJECT_ROOT = File.realpath("#{__dir__}/../../")
FIXTURE_PATH = File.realpath("#{PROJECT_ROOT}/features/fixtures/#{Laravel.fixture}")

AfterConfiguration do |_config|
  MazeRunner.config.enforce_bugsnag_integrity = false

  # the laravel-latest fixture uses a different mechanism for installing the
  # bugsnag-laravel library (see 'setup-laravel-dev-fixture.sh')
  if Laravel.fixture != 'laravel-latest'
    if ENV["DEBUG"]
      puts "Installing bugsnag-laravel from '#{PROJECT_ROOT}' to '#{FIXTURE_PATH}'"
    end

    Utils.install_bugsnag(PROJECT_ROOT, FIXTURE_PATH)
  end
end

Before do
  ENV["BUGSNAG_API_KEY"] = $api_key
  ENV["BUGSNAG_ENDPOINT"] = "http://#{Utils.current_ip}:9339"
  Laravel.reset!
end

Before("@not-lumen") do
  skip_this_scenario if Laravel.fixture.start_with?("lumen")
end

Before("@not-laravel") do
  skip_this_scenario if Laravel.fixture.start_with?("laravel")
end
