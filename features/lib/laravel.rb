require 'net/http'
require 'yaml'
require 'json'
require 'tempfile'

class Laravel
  class << self
    attr_reader :last_response

    def reset!
      @last_response = nil
    end

    def navigate_to(route, attempts = 0)
      @last_response = Net::HTTP.get('localhost', route, fixture_port)
    rescue => e
      raise "Failed to navigate to #{route} (#{e})" if attempts > 15

      sleep 1
      navigate_to(route, attempts + 1)
    end

    def fixture
      ENV.fetch('LARAVEL_FIXTURE', 'laravel56')
    end

    def fixture_port
      @port ||= load_port_from_docker_compose
    end

    def major_version
      # the first "canonical segment" is the first digit of the version number,
      # aka the major version
      version.canonical_segments.first
    end

    def version
      @version ||= load_version_from_fixture
    end

    def lumen?
      fixture.start_with?("lumen")
    end

    def latest?
      fixture.end_with?("-latest")
    end

    def supports_sessions?
      return false if lumen?
      return false if fixture == "laravel51"

      true
    end

    # the command to run the queue worker for a single job
    def queue_worker_once_command(tries)
      if version < "5.3.0"
        "php artisan queue:work --tries=#{tries}"
      else
        "php artisan queue:work --once --tries=#{tries}"
      end
    end

    # the command to run the queue worker as a daemon
    def queue_worker_daemon_command(tries)
      # the command to run the queue worker was 'queue:listen' but changed to
      # 'queue:work' in Laravel 5.3 ('queue:work' exists on older Laravels, but
      # is not quite equivalent)
      if version < "5.3.0"
        "php artisan queue:listen --tries=#{tries}"
      else
        "php artisan queue:work --tries=#{tries}"
      end
    end

    private

    def load_port_from_docker_compose
      compose_file = YAML.safe_load(File.read(Maze::Docker.singleton_class::COMPOSE_FILENAME))
      service = compose_file.fetch("services").fetch(ENV['LARAVEL_FIXTURE'])

      service.fetch("ports").first.fetch("published")
    end

    def load_version_from_fixture
      # get and parse the composer.lock file from the fixture
      composer_lock = Tempfile.create("#{fixture}-composer.lock") do |file|
        # copy the composer lock file out of the fixture so we can read it
        Maze::Docker.copy_from_container(fixture, from: "/app/composer.lock", to: file.path)

        # 'file.read' won't reflect the changes made by docker cp, so we use
        # JSON.load_file to reload the file & parse it
        JSON.load_file(file.path)
      end

      framework_section = composer_lock["packages"].find { |package| package["name"] == framework_package_name }
      version = framework_section["version"].delete_prefix("v")

      Gem::Version.new(version)
    end

    # the composer package name of the framework being used (Lumen or Laravel)
    def framework_package_name
      if lumen?
        "laravel/lumen-framework"
      else
        "laravel/framework"
      end
    end
  end
end
