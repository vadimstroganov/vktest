role :app, %w{deploy@62.109.14.129}
role :web, %w{deploy@62.109.14.129}

server '62.109.14.129', user: 'deploy', roles: %w{web app}

set :branch, 'master'
set :deploy_to, '/home/deploy/api.vk'
