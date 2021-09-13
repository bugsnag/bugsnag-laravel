require 'net/http'
require 'yaml'

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
      # e.g. laravel56 -> 5, lumen8 -> 8
      Integer(/^(?:laravel|lumen)(\d)/.match(fixture)[1])
    end

    def lumen?
      fixture.start_with?("lumen")
    end

    private

    def load_port_from_docker_compose
      compose_file = YAML.safe_load(File.read(Docker.singleton_class::COMPOSE_FILENAME))
      service = compose_file.fetch("services").fetch(ENV['LARAVEL_FIXTURE'])

      service.fetch("ports").first.fetch("published")
    end
  end
end
