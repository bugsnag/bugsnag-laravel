require 'os'
require 'pp'

$created_files = []

Before do
  find_default_docker_compose
end

def create_git_package(target_dir)
  pp target_dir
  excludes = ['.', '..', 'features', 'maze_output', 'example', 'vendor', 'composer.lock', 'Gemfile', 'Gemfile.lock']
  Dir.entries('.').each do |filename|
    next if excludes.include? filename
    $created_files << target_dir + '/' + filename
    if File.directory?(filename)
      FileUtils.copy_entry(filename, target_dir + '/' + filename, remove_destination: true)
    else
      FileUtils.cp(filename, target_dir)
    end
  end
end

def remove_created_packages
  $created_files.each do |file|
    if File.directory?(file)
      `rm -rf #{file}`
    else
      `rm -f #{file}`
    end
  end
end

After do
  remove_created_packages
end