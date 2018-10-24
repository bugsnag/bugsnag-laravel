When(/^I setup my package in the "(.*)" fixture$/) do |fixture|
  create_git_package("features/fixtures/#{fixture}/bugsnag-lib")
end