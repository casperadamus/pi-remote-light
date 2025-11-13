// Netlify function to control Pi via SSH
const { exec } = require('child_process');
const util = require('util');

exports.handler = async (event, context) => {
  // Set CORS headers
  const headers = {
    'Access-Control-Allow-Origin': '*',
    'Access-Control-Allow-Headers': 'Content-Type',
    'Access-Control-Allow-Methods': 'POST, OPTIONS',
    'Content-Type': 'application/json'
  };

  // Handle preflight OPTIONS request
  if (event.httpMethod === 'OPTIONS') {
    return {
      statusCode: 200,
      headers,
      body: ''
    };
  }

  if (event.httpMethod !== 'POST') {
    return {
      statusCode: 405,
      headers,
      body: JSON.stringify({ 
        success: false, 
        message: 'Method not allowed' 
      })
    };
  }

  try {
    // Pi connection details - matching your Laravel setup
    const PI_IP = '100.94.110.127';
    const PI_USER = 'casperadamus';
    const PI_PASS = '1016';
    const COMMAND = 'python3 lighton.py';
    
    console.log('Attempting SSH connection to Pi at:', PI_IP);
    
    // Use SSH to connect to Pi and execute command
    const execPromise = util.promisify(exec);
    
    // SSH command with password authentication
    // Note: In production, you might want to use SSH keys instead of passwords
    const sshCommand = `sshpass -p '${PI_PASS}' ssh -o StrictHostKeyChecking=no -o ConnectTimeout=10 ${PI_USER}@${PI_IP} '${COMMAND} > /tmp/script.log 2>&1 &'`;
    
    try {
      // Execute SSH command with timeout
      const { stdout, stderr } = await Promise.race([
        execPromise(sshCommand),
        new Promise((_, reject) => 
          setTimeout(() => reject(new Error('SSH connection timeout')), 15000)
        )
      ]);
      
      console.log('SSH command executed successfully');
      if (stderr) {
        console.log('SSH stderr:', stderr);
      }
      
      return {
        statusCode: 200,
        headers,
        body: JSON.stringify({
          success: true,
          message: '✅ Light command sent to Pi successfully via SSH!',
          timestamp: new Date().toISOString(),
          method: 'SSH',
          command: COMMAND
        })
      };
      
    } catch (sshError) {
      console.error('SSH Error:', sshError);
      
      let errorMessage = 'SSH connection failed';
      if (sshError.message.includes('timeout')) {
        errorMessage = 'SSH connection timeout - Pi may be offline';
      } else if (sshError.message.includes('Permission denied')) {
        errorMessage = 'SSH authentication failed - check credentials';
      } else if (sshError.message.includes('No route to host')) {
        errorMessage = 'Cannot reach Pi - check IP address and network';
      } else if (sshError.message.includes('sshpass: command not found')) {
        errorMessage = 'SSH tools not available in serverless environment';
      }
      
      return {
        statusCode: 200,
        headers,
        body: JSON.stringify({
          success: false,
          message: `❌ ${errorMessage}: ${sshError.message}`,
          timestamp: new Date().toISOString(),
          debug: {
            error: sshError.message,
            command: 'sshpass ssh command',
            pi_ip: PI_IP
          }
        })
      };
    }

  } catch (error) {
    console.error('Function Error:', error);
    return {
      statusCode: 500,
      headers,
      body: JSON.stringify({
        success: false,
        message: 'Server error: ' + error.message
      })
    };
  }
};
