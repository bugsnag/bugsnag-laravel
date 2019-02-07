require 'os'
require 'json'

# Copy bugsnag-laravel into fixture directory
VENDORED_LIB = 'features/fixtures/laravel/bugsnag-laravel.zip'
`composer archive -f zip --dir=#{File.dirname(VENDORED_LIB)} --file=#{File.basename(VENDORED_LIB, '.zip')}`

# Remove any locally installed composer deps
FileUtils.rm_rf('features/fixtures/laravel/vendor')

# Copy current requirements into fixture requirements
File.open('composer.json', 'r') do |source|
  parsed_composer = JSON.parse source.read
  requirements = parsed_composer["require"]
  repositories = parsed_composer["repositories"]
  File.open('features/fixtures/laravel/composer.json.template', 'r') do |template|
    parsed_template = JSON.parse template.read
    parsed_template["repositories"][0]["package"]["require"] = requirements
    parsed_template["repositories"].concat(repositories)
    File.open('features/fixtures/laravel/composer.json', 'w') do |target|
      target.write(JSON.pretty_generate(parsed_template))
    end
  end
end


Before do
  find_default_docker_compose
end

at_exit do
  FileUtils.rm_rf(VENDORED_LIB)
end
