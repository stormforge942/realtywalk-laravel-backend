const files = require.context(
  '.', // current directory
  true, // subdirectories
  /.+\.json$/ // only .json
)
let messages = {}
files.keys().forEach(fileName => {
  const path = fileName.replace('.json', '').replace('./', '').split('/')
  messages[path[0]] = messages[path[0]] || {}
  messages[path[0]][path[1]] = files(fileName)
})

export default messages
