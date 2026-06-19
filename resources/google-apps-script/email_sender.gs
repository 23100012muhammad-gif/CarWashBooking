// Google Apps Script code
// Save this as a new Google Apps Script project

// Set this script to execute as web app and copy the deployment URL
// Don't forget to set APP_SECRET in Script Properties

function doPost(e) {
  // Verify content type
  if (e.postData.type !== "application/json") {
    return ContentService.createTextOutput(JSON.stringify({
      status: 'error',
      message: 'Invalid content type'
    })).setMimeType(ContentService.MimeType.JSON);
  }

  // Parse request data
  var data;
  try {
    data = JSON.parse(e.postData.contents);
  } catch (error) {
    return ContentService.createTextOutput(JSON.stringify({
      status: 'error',
      message: 'Invalid JSON'
    })).setMimeType(ContentService.MimeType.JSON);
  }

  // Verify secret token
  var appSecret = PropertiesService.getScriptProperties().getProperty('APP_SECRET');
  if (!appSecret || data.secret !== appSecret) {
    return ContentService.createTextOutput(JSON.stringify({
      status: 'error',
      message: 'Invalid secret token'
    })).setMimeType(ContentService.MimeType.JSON);
  }

  // Validate required fields
  if (!data.to || !data.subject || !data.body) {
    return ContentService.createTextOutput(JSON.stringify({
      status: 'error',
      message: 'Missing required fields'
    })).setMimeType(ContentService.MimeType.JSON);
  }

  try {
    // Send email
    GmailApp.sendEmail(
      data.to,
      data.subject,
      data.body, // Plain text version
      {
        htmlBody: data.body, // HTML version (same as text in this case)
        name: "CarWash Connect", // Sender name
        replyTo: "noreply@carwashconnect.com"
      }
    );

    // Log success
    console.log('Email sent successfully to ' + data.to);

    return ContentService.createTextOutput(JSON.stringify({
      status: 'success',
      message: 'Email sent successfully'
    })).setMimeType(ContentService.MimeType.JSON);

  } catch (error) {
    // Log error
    console.error('Failed to send email: ' + error.toString());
    
    return ContentService.createTextOutput(JSON.stringify({
      status: 'error',
      message: 'Failed to send email: ' + error.toString()
    })).setMimeType(ContentService.MimeType.JSON);
  }
}

/**
 * Test function to verify setup
 */
function testSetup() {
  var secret = PropertiesService.getScriptProperties().getProperty('APP_SECRET');
  if (!secret) {
    throw new Error('APP_SECRET not set in Script Properties');
  }
  Logger.log('Setup verified successfully. APP_SECRET is configured.');
}