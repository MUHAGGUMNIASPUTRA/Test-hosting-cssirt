module.exports = {
  apps: [{
    name: 'csirt-ssr',
    script: 'bootstrap/ssr/ssr.js',
    instances: 1,
    exec_mode: 'fork',
    env: {
      NODE_ENV: 'production',
      PORT: 13714
    },
    error_file: './logs/ssr-error.log',
    out_file: './logs/ssr-out.log',
    log_file: './logs/ssr-combined.log',
    time: true,
    restart_delay: 4000,
    max_restarts: 10,
    watch: false,
    ignore_watch: [
      'node_modules',
      'logs',
      'storage',
      'public'
    ]
  }]
}
