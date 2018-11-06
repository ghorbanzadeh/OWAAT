
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `asvs`
--

-- --------------------------------------------------------

--
-- Table structure for table `assessment`
--

CREATE TABLE IF NOT EXISTS `assessment` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `user_id` mediumint(9) NOT NULL,
  `assessment_name` char(50) NOT NULL,
  `description` text NOT NULL,
  `complete` tinyint(1) NOT NULL DEFAULT '0',
  `create_time` datetime NOT NULL,
  `complete_time` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `assessment_rules`
--

CREATE TABLE IF NOT EXISTS `assessment_rules` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `assignment_id` mediumint(9) NOT NULL,
  `rule_id` mediumint(9) NOT NULL,
  `PassOrFail` tinyint(1) NOT NULL,
  `comment` text NOT NULL,
  `last_modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Table structure for table `assignment`
--

CREATE TABLE IF NOT EXISTS `assignment` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `user_id` mediumint(9) NOT NULL,
  `assessment_id` mediumint(9) NOT NULL,
  `status` mediumint(9) NOT NULL,
  `admin_comment` text NOT NULL,
  `user_comment` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `assignment_chapter`
--

CREATE TABLE IF NOT EXISTS `assignment_chapter` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `chapter_id` mediumint(9) NOT NULL,
  `assignment_id` mediumint(9) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `assignment_time` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Table structure for table `chapters`
--

CREATE TABLE IF NOT EXISTS `chapters` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `chapter_name` char(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=18 ;

--
-- Dumping data for table `chapters`
--

INSERT INTO `chapters` (`id`, `chapter_name`) VALUES
(1, 'Architecture'),
(2, 'Authentication'),
(3, 'Session Management'),
(4, 'Access Control'),
(5, 'Input validation and output encoding'),
(7, 'Cryptography'),
(8, 'Error Handling'),
(9, 'Data Protection'),
(10, 'Communications'),
(13, 'Malicious Code'),
(15, 'Business Logic Flaws'),
(16, 'Files and Resources'),
(17, 'Mobile'),
(18, 'API'),
(19, 'Configuration'),
(20, 'Internet of Things');


-- --------------------------------------------------------

--
-- Table structure for table `logging`
--

CREATE TABLE IF NOT EXISTS `logging` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `user_id` mediumint(9) NOT NULL,
  `ip` char(15) NOT NULL,
  `data` text NOT NULL,
  `time` datetime NOT NULL,
  `action` mediumint(9) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=42 ;

-- --------------------------------------------------------

--
-- Table structure for table `logging_action`
--

CREATE TABLE IF NOT EXISTS `logging_action` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `action` char(40) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `logging_action`
--

INSERT INTO `logging_action` (`id`, `action`) VALUES
(1, 'Successful Login'),
(2, 'Failed Login'),
(3, 'Logout');

-- --------------------------------------------------------

--
-- Table structure for table `report_rules`
--

CREATE TABLE IF NOT EXISTS `report_rules` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `assignment_id` mediumint(9) NOT NULL,
  `rule_id` mediumint(9) NOT NULL,
  `PassOrFail` tinyint(1) NOT NULL,
  `comment` text NOT NULL,
  `last_modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Table structure for table `rules`
--

CREATE TABLE IF NOT EXISTS `rules` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `chapter_id` mediumint(9) NOT NULL,
  `rule_number` mediumint(9) NOT NULL,
  `title` text NOT NULL,
  `level` mediumint(1) NOT NULL,
  `methodology` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=169 ;

--
-- Dumping data for table `rules`
--

INSERT INTO `rules` (`chapter_id`, `rule_number`, `title`, `level`) VALUES
-- V1 Architecture, Design and Threat Modeling Requirements
(1, 1,  'Verify that all application components are identified and are known to be needed.', 1),
(1, 2,  'Verify that all components, such as libraries, modules, and external systems, that are not part of the application but that the application relies on to operate are identified.', 2),
(1, 3,  'Verify that a high-level architecture for the application has been defined.', 2),
(1, 4,  'Verify that all application components are defined in terms of the business functions and/or security functions they provide.', 3),
(1, 5,  'Verify that all components that are not part of the application but that the application relies on to operate are defined in terms of the functions, and/or security functions, they provide.', 3),
(1, 6,  'Verify that a threat model for the target application has been produced and covers off risks associated with Spoofing, Tampering, Repudiation, Information Disclosure, Denial of Service, and Elevation of privilege (STRIDE).', 3),
(1, 7,  'Verify all security controls (including libraries that call external security services) have a centralized implementation.', 2),
(1, 8,  'Verify that components are segregated from each other via a defined security control, such as network segmentation, firewall rules, or cloud based security groups.', 2),
(1, 9,  'Verify the application has a clear separation between the data layer, controller layer and the display layer, such that security decisions can be enforced on trusted systems.', 2),
(1, 10, 'Verify that there is no sensitive business logic, secret keys or other proprietary information in client side code.', 2),
(1, 11, 'Verify that all application components, libraries, modules, frameworks, platform, and operating systems are free from known vulnerabilities.', 2),
-- V2: Authentication Verification Requirements
(2, 1,  'Verify all pages and resources by default require authentication except those specifically intended to be public (Principle of complete mediation).', 1),
(2, 2,  'Verify that the application does not automatically fill in credentials – either as hidden fields, URL arguments, Ajax requests, or in forms, as this implies plain text, reversible or de-cryptable password storage. Random time limited nonces are acceptable as stand ins, such as to protect change password forms or forgot password forms.', 1),
(2, 4,  'Verify all authentication controls are enforced on the server side.', 1),
(2, 6,  'Verify all authentication controls fail securely to ensure attackers cannot log in.', 1),
(2, 7,  'Verify password entry fields allow, or encourage, the use of passphrases, and do not prevent password managers, long passphrases or highly complex passwords being entered.', 1),
(2, 8,  'Verify all account identity authentication functions (such as update profile, forgot password, disabled / lost token, help desk or IVR) that might regain access to the account are at least as resistant to attack as the primary authentication mechanism.', 1),
(2, 9,  'Verify that the changing password functionality includes the old password, the new password, and a password confirmation.', 1),
(2, 12, 'Verify that all authentication decisions can be logged, without storing sensitive session identifiers or passwords. This should include requests with relevant metadata needed for security investigations.', 2),
(2, 13, 'Verify that account passwords are one way hashed with a salt, and there is sufficient work factor to defeat brute force and password hash recovery attacks.', 2),
(2, 16, 'Verify that credentials are transported using a suitable encrypted link and that all pages/functions that require a user to enter credentials are done so using an encrypted link.', 1),
(2, 17, 'Verify that the forgotten password function and other recovery paths do not reveal the current password and that the new password is not sent in clear text to the user.', 1),
(2, 18, 'Verify that information enumeration is not possible via login, password reset, or forgot account functionality.', 1),
(2, 19, 'Verify there are no default passwords in use for the application framework or any components used by the application (such as “admin/password”).', 1),
(2, 20, 'Verify that anti-automation is in place to prevent breached credential testing, brute forcing, and account lockout attacks.', 1),
(2, 21, 'Verify that all authentication credentials for accessing services external to the application are encrypted and stored in a protected location.', 2),
(2, 22, 'Verify that forgotten password and other recovery paths use a TOTP or other soft token, mobile push, or other offline recovery mechanism. Use of a random value in an e-mail or SMS should be a last resort and is known weak.', 1),
(2, 23, 'Verify that account lockout is divided into soft and hard lock status, and these are not mutually exclusive. If an account is temporarily soft locked out due to a brute force attack, this should not reset the hard lock status.', 2),
(2, 24, 'Verify that if shared knowledge based questions (also known as ''secret questions'') are required, the questions do not violate privacy laws and are sufficiently strong to protect accounts from malicious recovery.', 1),
(2, 25, 'Verify that high value applications can be configured to disallow the use of a configurable number of previous passwords.', 2),
(2, 26, 'Verify that risk based re-authentication, two factor or transaction signing is in place for high value transactions.', 2),
(2, 27, 'Verify that measures are in place to block the use of commonly chosen passwords and weak passphrases.', 1),
(2, 28, 'Verify that all authentication challenges, whether successful or failed, should respond in the same average response time.', 3),
(2, 29, 'Verify that secrets, API keys, and passwords are not included in the source code, or online source code repositories.', 3),
(2, 31, 'Verify that users can enrol and use TOTP verification, two-factor, biometric (Touch ID or similar), or equivalent multi-factor authentication mechanism that provides protection against single factor credential disclosure.', 2),
(2, 32, 'Verify that administrative interfaces are not accessible to untrusted parties.', 1),
(2, 33, 'Verify that the application is compatible with browser based and third party password managers, unless prohibited by risk based policy.', 1),
-- V3 Session Management Verification Requirements
(3, 1,  'Verify that there is no custom session manager, or that the custom session manager is resistant against all common session management attacks.', 1),
(3, 2,  'Verify that sessions are invalidated when the user logs out.', 1),
(3, 3,  'Verify that sessions timeout after a specified period of inactivity.', 1),
(3, 4,  'Verify that sessions timeout after an administratively-configurable maximum time period regardless of activity (an absolute timeout).', 2),
(3, 5,  'Verify that all pages that require authentication have easy and visible access to logout functionality.', 1),
(3, 6,  'Verify that the session id is never disclosed in URLs, error messages, or logs. This includes verifying that the application does not support URL rewriting of session cookies.', 1),
(3, 7,  'Verify that all successful authentication and re-authentication generates a new session and session id.', 1),
(3, 10, 'Verify that only session ids generated by the application framework are recognized as active by the application.', 2),
(3, 11, 'Verify that session ids are sufficiently long, random and unique across the correct active session base.', 1),
(3, 12, 'Verify that session ids stored in cookies have their path set to an appropriately restrictive value for the application, and authentication session tokens additionally set the “HttpOnly” and “secure” attributes', 1),
(3, 16, 'Verify that high value applications limit the number of active concurrent sessions.', 3),
(3, 17, 'Verify that an active session list is displayed in the account profile or similar of each user. The user should be able to terminate any active session.', 2),
(3, 18, 'Verify the for high value applications, that the user is prompted with the option to terminate all other active sessions after a successful change password process.', 3),
-- V4 Access Control Verification Requirements
(4, 1,  'Verify that the principle of least privilege exists - users should only be able to access functions, data files, URLs, controllers, services, and other resources, for which they possess specific authorization. This implies protection against spoofing and elevation of privilege.', 1),
(4, 4,  'Verify that access to sensitive records is protected, such that only authorized objects or data is accessible to each user (for example, protect against users tampering with a parameter to see or alter another user''s account). ', 1),
(4, 5,  'Verify that directory browsing is disabled unless deliberately desired. Additionally, applications should not allow discovery or disclosure of file or directory metadata, such as Thumbs.db, .DS_Store, .git or .svn folders.', 1),
(4, 8,  'Verify that access controls fail securely.', 1),
(4, 9,  'Verify that the same access control rules implied by the presentation layer are enforced on the server side.', 1),
(4, 10, 'Verify that all user and data attributes and policy information used by access controls cannot be manipulated by end users unless specifically authorized.', 2),
(4, 11, 'Verify that there is a centralized mechanism (including libraries that call external authorization services) for protecting access to each type of protected resource.', 3),
(4, 12, 'Verify that all access control decisions can be logged and all failed decisions are logged.', 2),
(4, 13, 'Verify that the application or framework uses strong random anti-CSRF tokens or has another transaction protection mechanism. ', 1),
(4, 14, 'Verify the system can protect against aggregate or continuous access of secured functions, resources, or data. For example, consider the use of a resource governor to limit the number of edits per hour or to prevent the entire database from being scraped by an individual user.', 2),
(4, 15, 'Verify the application has additional authorization (such as step up or adaptive authentication) for lower value systems, and / or segregation of duties for high value applications to enforce anti-fraud controls as per the risk of application and past fraud.', 2),
(4, 16, 'Verify that the application correctly enforces context-sensitive authorisation so as to not allow unauthorised manipulation by means of parameter tampering. ', 1),
-- V5 Input Validation and Output Encoding Verification Requirements
(5, 1,  'Verify that the runtime environment is not susceptible to buffer overflows, or that security controls prevent buffer overflows.', 1),
(5, 3,  'Verify that server side input validation failures result in request rejection and are logged.', 1),
(5, 5,  'Verify that input validation routines are enforced on the server side. ', 1),
(5, 6,  'Verify that a single input validation control is used by the application for each type of data that is accepted.', 3),
(5, 10, 'Verify that all SQL queries, HQL, OSQL, NOSQL and stored procedures, calling of stored procedures are protected by the use of prepared statements or query parameterization, and thus not susceptible to SQL injection ', 1),
(5, 11, 'Verify that the application is not susceptible to LDAP Injection, or that security controls prevent LDAP Injection. ', 1),
(5, 12, 'Verify that the application is not susceptible to OS Command Injection, or that security controls prevent OS Command Injection.', 1),
(5, 13, 'Verify that the application is not susceptible to Remote File Inclusion (RFI) or Local File Inclusion (LFI) when content is used that is a path to a file. ', 1),
(5, 14, 'Verify that the application is not susceptible to common XML attacks, such as XPath query tampering, XML External Entity attacks, and XML injection attacks. ', 1),
(5, 15, 'Verify that all string variables placed into HTML or other web client code is either properly contextually encoded manually, or utilize templates that automatically contextually encode to ensure the application is not susceptible to reflected, stored or DOM Cross-Site Scripting (XSS) attacks.', 1),
(5, 16, 'Verify that if the application framework allows automatic mass parameter assignment (also called automatic variable binding) from the inbound request to a model, verify that security sensitive fields such as “accountBalance”, “role” or “password” are protected from malicious automatic binding.', 2),
(5, 17, 'Verify that the application has defenses against HTTP parameter pollution attacks, particularly if the application framework makes no distinction about the source of request parameters (GET, POST, cookies, headers, environment, etc.)', 2),
(5, 18, 'Verify that client side validation is used as a second line of defense, in addition to server side validation.', 2),
(5, 19, 'Verify that all input data is validated, not only HTML form fields but all sources of input such as REST calls, query parameters, HTTP headers, cookies, batch files, RSS feeds, etc; using positive validation (whitelisting), then lesser forms of validation such as grey listing (eliminating known bad strings), or rejecting bad inputs (blacklisting).', 2),
(5, 20, 'Verify that structured data is strongly typed and validated against a defined schema including allowed characters, length and pattern (e.g. credit card numbers or telephone, or validating that two related fields are reasonable, such as validating suburbs and zip or post codes match). ', 2),
(5, 21, 'Verify that unstructured data is sanitized to enforce generic safety measures such as allowed characters and length, and characters potentially harmful in given context should be escaped (e.g. natural names with Unicode or apostrophes, such as ねこ or O''Hara)', 2),
(5, 22, 'Verify that untrusted HTML from WYSIWYG editors or similar are properly sanitized with an HTML sanitizer and handle it appropriately according to the input validation task and encoding task. ', 1),
(5, 23, 'Verify that where an application uses auto-escaping templating AND auto-escaping is disabled, output should be manually contextually encoded or sanitized to prevent XSS.', 2),
(5, 24, 'Verify that where data is transferred from one DOM context to another, the transfer uses safe JavaScript methods, such as using innerText or .val.', 2),
(5, 25, 'Verify when parsing JSON in browsers, that JSON.parse is used to parse JSON on the client. Do not use eval() to parse JSON on the client.', 2),
(5, 26, 'Verify that authenticated data is cleared from client storage, such as the browser DOM, after the session is terminated.', 2),
-- V7 Cryptography Verification Requirements
(7, 2,  'Verify that all cryptographic modules fail securely, and errors are handled in a way that does not enable Padding Oracle.', 1),
(7, 6,  'Verify that all random numbers, random file names, random GUIDs, and random strings are generated using the cryptographic module’s approved random number generator when these random values are intended to be not guessable by an attacker.', 2),
(7, 7,  'Verify that cryptographic algorithms used by the application have been validated against FIPS 140-2 or an equivalent standard.', 1),
(7, 8,  'Verify that cryptographic modules operate in their approved mode according to their published security policies.', 3),
(7, 9,  'Verify that there is an explicit policy for how cryptographic keys are managed (e.g., generated, distributed, revoked, and expired). Verify that this key lifecycle is properly enforced.', 2),
(7, 11, 'Verify that all consumers of cryptographic services do not have direct access to key material. Isolate cryptographic processes, including master secrets and consider the use of a virtualized or physical hardware key vault (HSM).', 3),
(7, 12, 'Verify that sensitive and Personally Identifiable Information is stored encrypted at rest, and in transit.', 2),
(7, 13, 'Verify that sensitive passwords or key material maintained in memory is overwritten with zeros as soon as it is no longer required, to mitigate memory dumping attacks.', 2),
(7, 14, 'Verify that all keys and passwords are replaceable, and are generated or replaced at installation time.', 2),
(7, 15, 'Verify that random numbers are created with proper entropy even when the application is under heavy load, or that the application degrades gracefully in such circumstances.', 3),
-- V8 Error Handling and Logging Verification Requirements
(8, 1,  'Verify that the application does not output error messages or stack traces containing sensitive data that could assist an attacker, including session id, software/framework versions and personal information', 1),
(8, 2,  'Verify that error handling logic in security controls denies access by default.', 2),
(8, 3,  'Verify security logging controls provide the ability to log success and particularly failure events that are identified as security-relevant.', 2),
(8, 4,  'Verify that each log event includes necessary information that would allow for a detailed investigation of the timeline when an event happens.', 2),
(8, 5,  'Verify that all events that include untrusted data will not execute as code in the intended log viewing software.', 2),
(8, 6,  'Verify that security logs are protected from unauthorized access and modification.', 2),
(8, 7,  'Verify that the application does not log sensitive data as defined under local privacy laws or regulations, organizational sensitive data as defined by a risk assessment, or sensitive authentication data that could assist an attacker, including user’s session identifiers, passwords, hashes, or API tokens.', 2),
(8, 8,  'Verify that all non-printable symbols and field separators are properly encoded in log entries, to prevent log injection.', 3),
(8, 9,  'Verify that log fields from trusted and untrusted sources are distinguishable in log entries.', 3),
(8, 10, 'Verify that an audit log or similar allows for non-repudiation of key transactions.', 1),
(8, 11, 'Verify that security logs have some form of integrity checking or controls to prevent unauthorized modification.', 3),
(8, 12, 'Verify that logs are stored on a different partition than the application is running with proper log rotation.', 3),
(8, 13, 'Verify that time sources are synchronized to the correct time and time zone.', 1),
-- V9 Data Protection Verification Requirements
(9, 1,  'Verify that all forms containing sensitive information have disabled client side caching, including autocomplete features.', 1),
(9, 2,  'Verify that the list of sensitive data processed by the application is identified, and that there is an explicit policy for how access to this data must be controlled, encrypted and enforced under relevant data protection directives.', 3),
(9, 3,  'Verify that all sensitive data is sent to the server in the HTTP message body or headers (i.e., URL parameters are never used to send sensitive data).', 1),
(9, 4,  'Verify that the application sets sufficient anti-caching headers such that any sensitive and personal information displayed by the application or entered by the user should not be cached on disk by mainstream modern browsers (e.g. visit about:cache to review disk cache).', 1),
(9, 5,  'Verify that on the server, all cached or temporary copies of sensitive data stored are protected from unauthorized access or purged/invalidated after the authorized user accesses the sensitive data.', 2),
(9, 6,  'Verify that there is a method to remove each type of sensitive data from the application at the end of the required retention policy.', 3),
(9, 7,  'Verify the application minimizes the number of parameters in a request, such as hidden fields, Ajax variables, cookies and header values.', 2),
(9, 8,  'Verify the application has the ability to detect and alert on abnormal numbers of requests for data harvesting for an example screen scraping.', 3),
(9, 9,  'Verify that data stored in client side storage (such as HTML5 local storage, session storage, IndexedDB, regular cookies or Flash cookies) does not contain sensitive data or PII.', 1),
(9, 10, 'Verify accessing sensitive data is logged, if the data is collected under relevant data protection directives or where logging of accesses is required.', 2),
(9, 11, 'Verify that sensitive information maintained in memory is overwritten with zeros as soon as it is no longer required, to mitigate memory dumping attacks.', 2),
-- V10 Communications Verification Requirements
(10, 1,  'Verify that a path can be built from a trusted CA to each Transport Layer Security (TLS) server certificate, and that each server certificate is valid.', 1),
(10, 3,  'Verify that TLS is used for all connections (including both external and backend connections) that are authenticated or that involve sensitive data or functions, and does not fall back to insecure or unencrypted protocols. Ensure the strongest alternative is the preferred algorithm.', 1),
(10, 4,  'Verify that backend TLS connection failures are logged.', 3),
(10, 5,  'Verify that certificate paths are built and verified for all client certificates using configured trust anchors and revocation information.', 3),
(10, 6,  'Verify that all connections to external systems that involve sensitive information or functions are authenticated.', 2),
(10, 8,  'Verify that there is a single standard TLS implementation that is used by the application that is configured to operate in an approved mode of operation.', 3),
(10, 10, 'Verify that TLS certificate public key pinning (HPKP) is implemented with production and backup public keys. For more information, please see the references below.', 2),
(10, 11, 'Verify that HTTP Strict Transport Security headers are included on all requests and for all subdomains, such as Strict-Transport-Security: max-age=15724800; includeSubdomains', 1),
(10, 12, 'Verify that production website URL has been submitted to preloaded list of Strict Transport Security domains maintained by web browser vendors. Please see the references below.', 3),
(10, 13, 'Verify that perfect forward secrecy is configured to mitigate passive attackers recording traffic.', 1),
(10, 14, 'Verify that proper certification revocation, such as Online Certificate Status Protocol (OCSP) Stapling, is enabled and configured.', 1),
(10, 15, 'Verify that only strong algorithms, ciphers, and protocols are used, through all the certificate hierarchy, including root and intermediary certificates of your selected certifying authority.', 1),
(10, 16, 'Verify that the TLS settings are in line with current leading practice, particularly as common configurations, ciphers, and algorithms become insecure.', 1),
-- V11 HTTP security configuration verification requirements
(11, 1,  'Verify that the application accepts only a defined set of required HTTP request methods, such as GET and POST are accepted, and unused methods (e.g. TRACE, PUT, and DELETE) are explicitly blocked.', 1),
(11, 2,  'Verify that every HTTP response contains a content type header specifying a safe character set (e.g., UTF-8, ISO 8859-1).', 1),
(11, 3,  'Verify that HTTP headers added by a trusted proxy or SSO devices, such as a bearer token, are authenticated by the application.', 2),
(11, 4,  'Verify that a suitable X-FRAME-OPTIONS header is in use for sites where content should not be viewed in a 3rd-party X-Frame.', 2),
(11, 5,  'Verify that the HTTP headers or any part of the HTTP response do not expose detailed version information of system components.', 1),
(11, 6,  'Verify that all API responses contain X-Content-Type-Options: nosniff and Content-Disposition: attachment; filename=''api.json'' (or other appropriate filename for the content type).', 1),
(11, 7,  'Verify that a content security policy (CSPv2) is in place that helps mitigate common DOM, XSS, JSON, and JavaScript injection vulnerabilities.', 1),
(11, 8,  'Verify that the X-XSS-Protection: 1; mode=block header is in place to enable browser reflected XSS filters.', 1),
-- V13 Malicious Code Verification Requirements
(13, 1,  'Verify all malicious activity is adequately sandboxed, containerized or isolated to delay and deter attackers from attacking other applications.', 3),
(13, 2,  'Verify that the application source code, and as many third party libraries as possible, does not contain back doors, Easter eggs, and logic flaws in authentication, access control, input validation, and the business logic of high value transactions.', 3),
-- V15 Business Logic Verification Requirements
(15, 1, 'Verify the application will only process business logic flows in sequential step order, with all steps being processed in realistic human time, and not process out of order, skipped steps, process steps from another user, or too quickly submitted transactions.', 2),
(15, 2, 'Verify the application has business limits and correctly enforces on a per user basis, with configurable alerting and automated reactions to automated or unusual attack.', 2),
-- V16 File and Resources Verification Requirements
(16, 1,  'Verify that URL redirects and forwards only allow whitelisted destinations, or show a warning when redirecting to potentially untrusted content.', 1),
(16, 2,  'Verify that untrusted file data submitted to the application is not used directly with file I/O commands, particularly to protect against path traversal, local file include, file mime type, reflective file download, and OS command injection vulnerabilities.', 1),
(16, 3,  'Verify that files obtained from untrusted sources are validated to be of expected type and scanned by antivirus scanners to prevent upload of known malicious content.', 1),
(16, 4,  'Verify that untrusted data is not used within inclusion, class loader, or reflection capabilities to prevent remote/local code execution vulnerabilities.', 1),
(16, 5,  'Verify that untrusted data is not used within cross-domain resource sharing (CORS) to protect against arbitrary remote content.', 1),
(16, 6,  'Verify that files obtained from untrusted sources are stored outside the webroot, with limited permissions, preferably with strong validation.', 2),
(16, 7,  'Verify that the web or application server is configured by default to deny access to remote resources or systems outside the web or application server.', 2),
(16, 8,  'Verify the application code does not execute uploaded data obtained from untrusted sources.', 1),
(16, 9,  'Verify that unsupported, insecure or deprecated client-side technologies are not used, such as NSAPI plugins, Flash, Shockwave, Active-X, Silverlight, NACL, or client-side Java applets.', 1),
-- V17 Mobile Verification Requirements
(17, 1,  'Verify that ID values stored on the device and retrievable by other applications, such as the UDID or IMEI number are not used as authentication tokens.', 1),
(17, 2,  'Verify that the mobile app does not store sensitive data onto potentially unencrypted shared resources on the device (e.g. SD card or shared folders).', 1),
(17, 3,  'Verify that sensitive data is not stored unprotected on the device, even in system protected areas such as key chains.', 1),
(17, 4,  'Verify that secret keys, API tokens, or passwords are dynamically generated in mobile applications.', 1),
(17, 5,  'Verify that the mobile app prevents leaking of sensitive information (for example, screenshots are saved of the current application as the application is backgrounded or writing sensitive information in console).', 2),
(17, 6,  'Verify that the application is requesting minimal permissions for required functionality and resources.', 2),
(17, 7,  'Verify that the application sensitive code is laid out unpredictably in memory (For example ASLR).', 1),
(17, 8,  'Verify that there are anti-debugging techniques present that are sufficient enough to deter or delay likely attackers from injecting debuggers into the mobile app (For example GDB).', 3),
(17, 9,  'Verify that the app does not export sensitive activities, intents, or content providers for other mobile apps on the same device to exploit.', 1),
(17, 10, 'Verify that sensitive information maintained in memory is overwritten with zeros as soon as it is no longer required, to mitigate memory dumping attacks.', 2),
(17, 11, 'Verify that the app validates input to exported activities, intents, or content providers.', 1),
-- V18 API and Web Service Verification Requirements
(18, 1,  'Verify that the same encoding style is used between the client and the server.', 1),
(18, 2,  'Verify that access to administration and management functions within the Web Service Application is limited to web service administrators.', 1),
(18, 3,  'Verify that XML or JSON schema is in place and verified before accepting input.', 1),
(18, 4,  'Verify that all input is limited to an appropriate size limit.', 1),
(18, 5,  'Verify that SOAP based web services are compliant with Web Services-Interoperability (WS-I) Basic Profile at minimum. This essentially means TLS encryption.', 1),
(18, 6,  'Verify the use of session-based authentication and authorization. Please refer to sections 2, 3 and 4 for further guidance. Avoid the use of static ''API keys'' and similar.', 1),
(18, 7,  'Verify that the REST service is protected from Cross-Site Request Forgery via the use of at least one or more of the following: ORIGIN checks, double submit cookie pattern, CSRF nonces, and referrer checks.', 1),
(18, 8,  'Verify the REST service explicitly check the incoming Content-Type to be the expected one, such as application/xml or application/json.', 2),
(18, 9,  'Verify that the message payload is signed to ensure reliable transport between client and service, using JSON Web Signing or WS-Security for SOAP requests.', 2),
(18, 10, 'Verify that alternative and less secure access paths do not exist.', 2),
-- V19 Configuration Verification Requirements
(19, 1,  'Verify that all components are up to date with proper security configuration(s) and version(s). This should include removal of unneeded configurations and folders such as sample applications, platform documentation, and default or example users. ', 1),
(19, 2,  'Verify that communications between components, such as between the application server and the database server, are encrypted, particularly when the components are in different containers or on different systems.', 2),
(19, 3,  'Verify that communications between components, such as between the application server and the database server, is authenticated using an account with the least necessary privileges.', 2),
(19, 4,  'Verify application deployments are adequately sandboxed, containerized or isolated to delay and deter attackers from attacking other applications.', 2),
(19, 5,  'Verify that the application build and deployment processes are performed in a secure and repeatable method, such as CI / CD automation and automated configuration management.', 2),
(19, 6,  'Verify that authorised administrators have the capability to verify the integrity of all security-relevant configurations to detect tampering.', 3),
(19, 7,  'Verify that all application components are signed.', 3),
(19, 8,  'Verify that third party components come from trusted repositories.', 3),
(19, 9,  'Verify that build processes for system level languages have all security flags enabled, such as ASLR, DEP, and security checks.', 3),
(19, 10, 'Verify that all application assets are hosted by the application, such as JavaScript libraries, CSS stylesheets and web fonts are hosted by the application rather than rely on a CDN or external provider.', 3),
(19, 11, 'Verify that all application components, services, and servers each use their own low privilege service account, that is not shared between applications nor used by administrators.', 2),
-- V20 Internet of Things Verification Requirements
(20, 1,  'Verify that application layer debugging interfaces such USB or serial are disabled.', 1),
(20, 2,  'Verify that cryptographic keys are unique to each individual device.', 1),
(20, 3,  'Verify that memory protection controls such as ASLR and DEP are enabled by the IoT operating system, if applicable.', 1),
(20, 4,  'Verify that on-chip debugging interfaces such as JTAG or SWD are disabled or that available protection mechanism is enabled and configured appropriately.', 1),
(20, 5,  'Verify that physical debug headers are not present on the device.', 1),
(20, 6,  'Verify that sensitive data is not stored unencrypted on the device.', 1),
(20, 7,  'Verify that the device prevents leaking of sensitive information.', 1),
(20, 8,  'Verify that the firmware apps protect data-in-transit using transport security.', 1),
(20, 9,  'Verify that the firmware apps validate the digital signature of server connections.', 1),
(20, 10, 'Verify that wireless communications are mutually authenticated.', 1),
(20, 11, 'Verify that wireless communications are sent over an encrypted channel.', 1),
(20, 12, 'Verify that the firmware apps pin the digital signature to a trusted server(s).', 2),
(20, 13, 'Verify the presence of physical tamper resistance and/or tamper detection features, including epoxy.', 2),
(20, 14, 'Verify that identifying markings on chips have been removed.', 2),
(20, 15, 'Verify that any available Intellectual Property protection technologies provided by the chip manufacturer are enabled.', 2),
(20, 16, 'Verify security controls are in place to hinder firmware reverse engineering (e.g., removal of verbose debugging strings).', 2),
(20, 17, 'Verify the device validates the boot image signature before loading.', 2),
(20, 18, 'Verify that the firmware update process is not vulnerable to time-of-check vs time-of-use attacks.', 2),
(20, 19, 'Verify the device uses code signing and validates firmware upgrade files before installing.', 2),
(20, 20, 'Verify that the device cannot be downgraded to old versions of valid firmware.', 2),
(20, 21, 'Verify usage of cryptographically secure pseudo-random number generator on embedded device (e.g., using chip-provided random number generators).', 2),
(20, 22, 'Verify that the device wipes firmware and sensitive data upon detection of tampering or receipt of invalid message.', 3),
(20, 23, 'Verify that only microcontrollers that support disabling debugging interfaces (e.g. JTAG, SWD) are used.', 3),
(20, 24, 'Verify that only microcontrollers that provide substantial protection from de-capping and side channel attacks are used.', 3),
(20, 25, 'Verify that sensitive traces are not exposed to outer layers of the printed circuit board.', 3),
(20, 26, 'Verify that inter-chip communication is encrypted.', 3),
(20, 27, 'Verify the device uses code signing and validates code before execution.', 3),
(20, 28, 'Verify that sensitive information maintained in memory is overwritten with zeros as soon as it is no longer required.', 3),
(20, 29, 'Verify that the firmware apps utilize kernel containers for isolation between apps.', 3);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `fname` char(30) NOT NULL,
  `lname` char(30) NOT NULL,
  `email` char(30) NOT NULL,
  `uname` char(30) NOT NULL,
  `password` char(40) NOT NULL,
  `administrator` tinyint(1) NOT NULL DEFAULT '0',
  `enabled` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;


/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
