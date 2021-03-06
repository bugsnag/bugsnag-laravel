require 'os'
require 'json'

# Copy bugsnag-laravel into fixture directories
FIXTURE_DIR = 'features/fixtures'
VENDORED_LIB = "#{FIXTURE_DIR}/bugsnag-laravel.zip"
FILES_TO_CLEANUP = [VENDORED_LIB]

`composer archive -f zip --dir=#{File.dirname(VENDORED_LIB)} --file=#{File.basename(VENDORED_LIB, '.zip')}`

Dir.glob("#{FIXTURE_DIR}/laravel*").each do |directory|
  next if directory.end_with?('laravel-latest')

  zip = "#{directory}/bugsnag-laravel.zip"
  FILES_TO_CLEANUP << zip

  FileUtils.cp(VENDORED_LIB, zip)

  # Remove any locally installed composer deps
  FileUtils.rm_rf("#{directory}/vendor")
end

# Copy current requirements into fixture requirements
File.open('composer.json', 'r') do |source|
  parsed_composer = JSON.parse(source.read)
  requirements = parsed_composer["require"]

  Dir.glob("#{FIXTURE_DIR}/laravel*").each do |directory|
    next if directory.end_with?('laravel-latest')

    File.open(directory + '/composer.json.template', 'r') do |template|
      parsed_template = JSON.parse template.read
      parsed_template["repositories"][0]["package"]["require"] = requirements

      composer_json = "#{directory}/composer.json"
      FILES_TO_CLEANUP << composer_json

      File.open(composer_json, 'w') do |target|
        target.write(JSON.pretty_generate(parsed_template))
      end
    end
  end
end

Before do
  ENV["BUGSNAG_API_KEY"] = $api_key
  ENV["BUGSNAG_ENDPOINT"] = "http://#{current_ip}:9339"
  Laravel.reset!
end

at_exit do
  project_root = File.dirname(File.dirname(__dir__))

  FILES_TO_CLEANUP.each do |file|
    FileUtils.rm_rf("#{project_root}/#{file}")
  end
end

AfterConfiguration do |_config|
  MazeRunner.config.enforce_bugsnag_integrity = false if MazeRunner.config.respond_to? :enforce_bugsnag_integrity=
end
