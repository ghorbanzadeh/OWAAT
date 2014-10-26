
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
(2, 'Authentication'),
(3, 'Session Management'),
(4, 'Access Control'),
(5, 'Malicious Input Handling'),
(7, 'Cryptography at Rest'),
(8, 'Error Handling and Logging'),
(9, 'Data Protection'),
(10, 'Communications'),
(11, 'HTTP'),
(13, 'Malicious Controls'),
(15, 'Business Logic'),
(16, 'File and Resource'),
(17, 'Mobile');

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

INSERT INTO `rules` (`id`, `chapter_id`, `rule_number`, `title`, `level`, `methodology`) VALUES
(1, 2, 1, 'Verify all pages and resources require authentication except those specifically intended to be public (Principle of complete mediation).', 1, ''),
(2, 2, 2, 'Verify all password fields do not echo the user''s password when it is entered.', 1, ''),
(3, 2, 4, 'Verify all authentication controls are enforced on the server side.', 1, ''),
(4, 2, 5, 'Verify all authentication controls (including libraries that call external authentication services) have a centralized implementation.', 3, ''),
(5, 2, 6, 'Verify all authentication controls fail securely to ensure attackers cannot log in.', 1, ''),
(6, 2, 7, 'Verify password entry fields allow or encourage the use of passphrases, and do not prevent long passphrases or highly complex passwords being entered, and provide a sufficient minimum strength to protect against the use of commonly chosen passwords.', 2, ''),
(7, 2, 8, 'Verify all account identity authentication functions (such as registration, update profile, forgot username, forgot password, disabled / lost token, help desk or IVR) that might regain access to the account are at least as resistant to attack as the primary authentication mechanism.', 2, ''),
(8, 2, 9, 'Verify users can safely change their credentials using a mechanism that is at least as resistant to attack as the primary authentication mechanism.', 2, ''),
(9, 2, 12, 'Verify that all authentication decisions are logged. This should include requests with missing required information, needed for security investigations.', 2, ''),
(10, 2, 13, 'Verify that account passwords are salted using a salt that is unique to that account (e.g., internal user ID, account creation) and use bcrypt, scrypt or PBKDF2 before storing the password.', 2, ''),
(11, 2, 16, 'Verify that credentials, and all other identity information handled by the application(s), do not traverse unencrypted or weakly encrypted links.', 1, ''),
(12, 2, 17, 'Verify that the forgotten password function and other recovery paths do not reveal the current password and that the new password is not sent in clear text to the user.', 1, ''),
(13, 2, 18, 'Verify that username enumeration is not possible via login, password reset, or forgot account functionality.', 1, ''),
(14, 2, 19, 'Verify there are no default passwords in use for the application framework or any components used by the application (such as "admin/password").', 1, ''),
(15, 2, 20, 'Verify that a resource governor is in place to protect against vertical (a single account tested against all possible passwords) and horizontal brute forcing (all accounts tested with the same password e.g. "Password1"). A correct credential entry should incur no delay. Both these governor mechanisms should be active simultaneously to protect against diagonal and distributed attacks.', 2, ''),
(16, 2, 21, 'Verify that all authentication credentials for accessing services external to the application are encrypted and stored in a protected location (not in source code).', 2, ''),
(17, 2, 22, 'Verify that forgot password and other recovery paths send a link including a time-limited activation token rather than the password itself. Additional authentication based on soft-tokens (e.g. SMS token, native mobile applications, etc.) can be required as well before the link is sent over.', 2, ''),
(18, 2, 23, 'Verify that forgot password functionality does not lock or otherwise disable the account until after the user has successfully changed their password. This is to prevent valid users from being locked out.', 2, ''),
(19, 2, 24, 'Verify that there are no shared knowledge questions/answers (so called "secret" questions and answers).', 2, ''),
(20, 2, 25, 'Verify that the system can be configured to disallow the use of a configurable number of previous passwords.', 2, ''),
(21, 2, 26, 'Verify re-authentication, step up or adaptive authentication, SMS or other two factor authentication, or transaction signing is required before any application-specific sensitive operations are permitted as per the risk profile of the application.', 3, ''),
(22, 3, 1, 'Verify that the framework''s default session management control implementation is used by the application.', 1, ''),
(23, 3, 2, 'Verify that sessions are invalidated when the user logs out.', 1, ''),
(24, 3, 3, 'Verify that sessions timeout after a specified period of inactivity.', 1, ''),
(25, 3, 4, 'Verify that sessions timeout after an administratively-configurable maximum time period regardless of activity (an absolute timeout).', 2, ''),
(26, 3, 5, 'Verify that all pages that require authentication to access them have logout links.', 1, ''),
(27, 3, 6, 'Verify that the session id is never disclosed other than in cookie headers; particularly in URLs, error messages, or logs. This includes verifying that the application does not support URL rewriting of session cookies.', 1, ''),
(28, 3, 7, 'Verify that the session id is changed on login to prevent session fixation.', 2, ''),
(29, 3, 8, 'Verify that the session id is changed upon re-authentication.', 2, ''),
(30, 3, 10, 'Verify that only session ids generated by the application framework are recognized as valid by the application.', 2, ''),
(31, 3, 11, 'Verify that authenticated session tokens are sufficiently long and random to withstand session guessing attacks.', 2, ''),
(32, 3, 12, 'Verify that authenticated session tokens using cookies have their path set to an appropriately restrictive value for that site. The domain cookie attribute restriction should not be set unless for a business requirement, such as single\r\nsign on.', 2, ''),
(33, 3, 14, 'Verify that authenticated session tokens using cookies sent via HTTP, are protected by the use of "HttpOnly".', 1, ''),
(34, 3, 15, 'Verify that authenticated session tokens using cookies are protected with the "secure" attribute and a strict transport security header (such as Strict-Transport-Security: max-age=60000; includeSubDomains) are present.', 1, ''),
(35, 3, 16, 'Verify that the application does not permit duplicate concurrent user sessions, originating from different machines.', 2, ''),
(36, 4, 1, 'Verify that users can only access secured functions or services for which they possess specific authorization.', 1, ''),
(37, 4, 2, 'Verify that users can only access secured URLs for which they possess specific authorization.', 1, ''),
(38, 4, 3, 'Verify that users can only access secured data files for which they possess specific authorization.', 1, ''),
(39, 4, 4, 'Verify that direct object references are protected, such that only authorized objects or data are accessible to each user (for example, protect against direct object reference tampering).', 1, ''),
(40, 4, 5, 'Verify that directory browsing is disabled unless deliberately desired.', 1, ''),
(41, 4, 8, 'Verify that access controls fail securely.', 1, ''),
(42, 4, 9, 'Verify that the same access control rules implied by the presentation layer are enforced on the server side for that user role, such that controls and parameters cannot be re-enabled or re-added from higher privilege users.', 2, ''),
(43, 4, 10, 'Verify that all user and data attributes and policy information used by access controls cannot be manipulated by end users unless specifically authorized.', 2, ''),
(44, 4, 11, 'Verify that all access controls are enforced on the server side.', 1, ''),
(45, 4, 12, 'Verify that there is a centralized mechanism (including libraries that call external authorization services) for protecting access to each type of protected resource.', 3, ''),
(46, 4, 14, 'Verify that all access control decisions are be logged and all failed decisions are logged.', 2, ''),
(47, 4, 16, 'Verify that the application or framework generates strong random anti-CSRF tokens unique to the user as part of all high value transactions or accessing sensitive data, and that the application verifies the presence of this token with the proper value for the current user when processing these requests.', 1, ''),
(48, 4, 17, 'Aggregate access control protection â€“ verify the system can protect against aggregate or continuous access of secured functions, resources, or data. For example, possibly by the use of a resource governor to limit the number of edits per hour or to prevent the entire database from being scraped by an individual user.', 2, ''),
(49, 5, 1, 'Verify that the runtime environment is not susceptible to buffer overflows, or that security controls prevent buffer overflows.', 1, ''),
(50, 5, 3, 'Verify that all input validation failures result in input rejection.', 1, ''),
(51, 5, 4, 'Verify that a character set, such as UTF-8, is specified for all sources of input.', 2, ''),
(52, 5, 5, 'Verify that all input validation or encoding routines are performed and enforced on the server side.', 1, ''),
(53, 5, 6, 'Verify that a single input validation control is used by the application for each type of data that is accepted.', 3, ''),
(54, 5, 7, 'Verify that all input validation failures are logged.', 3, ''),
(55, 5, 8, 'Verify that all input data is canonicalized for all downstream decoders or interpreters prior to validation.', 2, ''),
(56, 5, 10, 'Verify that the runtime environment is not susceptible to SQL Injection, or that security controls prevent SQL Injection.', 1, ''),
(57, 5, 11, 'Verify that the runtime environment is not susceptible to LDAP Injection, or that security controls prevent LDAP Injection.', 1, ''),
(58, 5, 12, 'Verify that the runtime environment is not susceptible to OS Command Injection, or that security controls prevent OS Command Injection.', 1, ''),
(59, 5, 13, 'Verify that the runtime environment is not susceptible to XML External Entity attacks or that security controls prevents XML External Entity attacks.', 1, ''),
(60, 5, 14, 'Verify that the runtime environment is not susceptible to XML Injections or that security controls prevents XML Injections.', 1, ''),
(61, 5, 16, 'Verify that all untrusted data that are output to HTML (including HTML elements, HTML attributes, JavaScript data values, CSS blocks, and URI attributes) are properly escaped for the applicable context.', 1, ''),
(62, 5, 17, 'If the application framework allows automatic mass parameter assignment (also called automatic variable binding) from the inbound request to a model, verify that security sensitive fields such as "accountBalance", "role" or "password" are protected from malicious automatic binding.', 2, ''),
(63, 5, 18, 'Verify that the application has defenses against HTTP parameter pollution attacks, particularly if the application framework makes no distinction about the source of request parameters (GET, POST, cookies, headers, environment, etc.)', 2, ''),
(64, 5, 19, 'Verify that for each type of output encoding/escaping performed by the application, there is a single security control for that type of output for the intended destination.', 3, ''),
(65, 7, 1, 'Verify that all cryptographic functions used to protect secrets from the application user are implemented server side.', 2, ''),
(66, 7, 2, 'Verify that all cryptographic modules fail securely.', 2, ''),
(67, 7, 3, 'Verify that access to any master secret(s) is protected from unauthorized access (A master secret is an application credential stored as plaintext on disk that is used to protect access to security configuration information).', 2, ''),
(68, 7, 6, 'Verify that all random numbers, random file names, random GUIDs, and random strings are generated using the cryptographic moduleâ€™s approved random number generator when these random values are intended to be unguessable by an attacker.', 2, ''),
(69, 7, 7, 'Verify that cryptographic modules used by the application have been validated against FIPS 140-2 or an equivalent standard.', 3, ''),
(70, 7, 8, 'Verify that cryptographic modules operate in their approved mode according to their published security policies.', 3, ''),
(71, 7, 9, 'Verify that there is an explicit policy for how cryptographic keys are managed (e.g., generated, distributed, revoked, expired). Verify that this policy is properly enforced.', 2, ''),
(72, 8, 1, 'Verify that the application does not output error messages or stack traces containing sensitive data that could assist an attacker, including session id and personal information.', 1, ''),
(73, 8, 2, 'Verify that all error handling is performed on trusted devices.', 2, ''),
(74, 8, 3, 'Verify that all logging controls are implemented on the server.', 2, ''),
(75, 8, 4, 'Verify that error handling logic in security controls denies access by default.', 2, ''),
(76, 8, 5, 'Verify security logging controls provide the ability to log both success and failure events that are identified as security-relevant.', 2, ''),
(77, 8, 6, 'Verify that each log event includes: a timestamp from a reliable source, severity level of the event, an indication that this is a security relevant event (if mixed with other logs), the identity of the user that caused the event (if there is a user associated with the event), the source IP address of the\r\nrequest associated with the event, whether the event succeeded or failed,\r\nand a description of the event.', 2, ''),
(78, 8, 7, 'Verify that all events that include untrusted data will not execute as code in the intended log viewing software.', 3, ''),
(79, 8, 8, 'Verify that security logs are protected from unauthorized access and modification.', 2, ''),
(80, 8, 9, 'Verify that there is a single application-level logging implementation that is used by the software.', 3, ''),
(81, 8, 10, 'Verify that the application does not log application-specific sensitive data that could assist an attacker, including user''s session identifiers and personal or sensitive information. The length and existence of sensitive data can be logged.', 2, ''),
(82, 8, 11, 'Verify that a log analysis tool is available which allows the analyst to search for log events based on combinations of search criteria across all fields in the log record format supported by this system.', 2, ''),
(83, 8, 13, 'Verify that all non-printable symbols and field separators are properly encoded in log entries, to prevent log injection.', 3, ''),
(84, 8, 14, 'Verify that log fields from trusted and untrusted sources are distinguishable in log entries.', 3, ''),
(85, 8, 15, 'Verify that logging is performed before executing the transaction. If logging was unsuccessful (e.g. disk full, insufficient permissions) the application fails safe. This is for when integrity and non-repudiation are a must.', 3, ''),
(86, 9, 1, 'Verify that all forms containing sensitive information have disabled client side caching, including autocomplete features.', 1, ''),
(87, 9, 2, 'Verify that the list of sensitive data processed by this application is identified, and that there is an explicit policy for how access to this data must be controlled, and when this data must be encrypted (both at rest and in transit). Verify that this policy is properly enforced.', 3, ''),
(88, 9, 3, 'Verify that all sensitive data is sent to the server in the HTTP message body (i.e., URL parameters are never used to send sensitive data).', 1, ''),
(89, 9, 4, 'Verify that all cached or temporary copies of sensitive data sent to the client are protected from unauthorized access or purged/invalidated after the authorized user accesses the sensitive data (e.g., the proper no-cache and no-store Cache-Control headers are set).', 2, ''),
(90, 9, 5, 'Verify that all cached or temporary copies of sensitive data stored on the server are protected from unauthorized access or purged/invalidated after the authorized user accesses the sensitive data.', 2, ''),
(91, 9, 6, 'Verify that there is a method to remove each type of sensitive data from the application at the end of its required retention period.', 3, ''),
(92, 9, 7, 'Verify the application minimizes the number of parameters sent to untrusted systems, such as hidden fields, Ajax variables, cookies and header values.', 3, ''),
(93, 9, 8, 'Verify the application has the ability to detect and alert on abnormal numbers of requests for information or processing high value transactions for that user role, such as screen scraping, automated use of web service extraction, or data loss prevention. For example, the average user should not be able to access more than 5 records per hour or 30 records per day, or add 10 friends to a social network per minute.', 3, ''),
(94, 10, 1, 'Verify that a path can be built from a trusted CA to each Transport Layer Security (TLS) server certificate, and that each server certificate is valid.', 1, ''),
(95, 10, 2, 'Verify that failed TLS connections do not fall back to an insecure HTTP connection.', 3, ''),
(96, 10, 3, 'Verify that TLS is used for all connections (including both external and backend connections) that are authenticated or that involve sensitive data or functions.', 2, ''),
(97, 10, 4, 'Verify that backend TLS connection failures are logged.', 2, ''),
(98, 10, 5, 'Verify that certificate paths are built and verified for all client certificates using configured trust anchors and revocation information.', 3, ''),
(99, 10, 6, 'Verify that all connections to external systems that involve sensitive information or functions are authenticated.', 2, ''),
(100, 10, 7, 'Verify that all connections to external systems that involve sensitive information or functions use an account that has been set up to have the minimum privileges necessary for the application to function properly.', 2, ''),
(101, 10, 8, 'Verify that there is a single standard TLS implementation that is used by the application that is configured to operate in an approved mode of operation (See http://csrc.nist.gov/groups/STM/cmvp/documents/fips140-2/FIPS1402IG.pdf ).', 3, ''),
(102, 10, 9, 'Verify that specific character encodings are defined for all connections (e.g., UTF-8).', 3, ''),
(103, 11, 2, 'Verify that the application accepts only a defined set of HTTP request methods, such as GET and POST and unused methods are explicitly blocked.', 1, ''),
(104, 11, 3, 'Verify that every HTTP response contains a content type header specifying a safe character set (e.g., UTF-8).', 1, ''),
(105, 11, 6, 'Verify that HTTP headers in both requests and responses contain only printable ASCII characters.', 2, ''),
(106, 11, 8, 'Verify that HTTP headers and / or other mechanisms for older browsers have been included to protect against clickjacking attacks.', 1, ''),
(107, 11, 9, 'Verify that HTTP headers added by a frontend (such as X-Real-IP), and used by the application, cannot be spoofed by the end user.', 2, ''),
(108, 11, 10, 'Verify that the HTTP header, X-Frame-Options is in use for sites where content should not be viewed in a 3rd-party X-Frame. A common middle ground is to send SAMEORIGIN, meaning only websites of the same origin may frame it.', 2, ''),
(109, 11, 12, 'Verify that the HTTP headers do not expose detailed version information of system components.', 2, ''),
(110, 13, 1, 'Verify that no malicious code is in any code that was either developed or modified in order to create the application.', 3, ''),
(111, 13, 2, 'Verify that the integrity of interpreted code, libraries, executables, and configuration files is verified using checksums or hashes.', 3, ''),
(112, 13, 3, 'Verify that all code implementing or using authentication controls is not affected by any malicious code.', 3, ''),
(113, 13, 4, 'Verify that all code implementing or using session management controls is not affected by any malicious code.', 3, ''),
(114, 13, 5, 'Verify that all code implementing or using access controls is not affected by any malicious code.', 3, ''),
(115, 13, 6, 'Verify that all input validation controls are not affected by any malicious code.', 3, ''),
(116, 13, 7, 'Verify that all code implementing or using output validation controls is not affected by any malicious code.', 3, ''),
(117, 13, 8, 'Verify that all code supporting or using a cryptographic module is not affected by any malicious code.', 3, ''),
(118, 13, 9, 'Verify that all code implementing or using error handling and logging controls is not affected by any malicious code.', 3, ''),
(119, 13, 10, 'Verify all malicious activity is adequately sandboxed.', 3, ''),
(120, 13, 11, 'Verify that sensitive data is rapidly sanitized from memory as soon as it is no longer needed and handled in accordance to functions and techniques supported by the framework/library/operating system.', 3, ''),
(121, 15, 1, 'Verify the application processes or verifies all high value business logic flows in a trusted environment, such as on a protected and monitored server.', 2, ''),
(122, 15, 2, 'Verify the application does not allow spoofed high value transactions, such as allowing Attacker User A to process a transaction as Victim User B by tampering with or replaying session, transaction state, transaction or user IDs.', 2, ''),
(123, 15, 3, 'Verify the application does not allow high value business logic parameters to be tampered with, such as (but not limited to): price, interest, discounts, PII, balances, stock IDs, etc.', 2, ''),
(124, 15, 4, 'Verify the application has defensive measures to protect against repudiation attacks, such as verifiable and protected transaction logs, audit trails or system logs, and in highest value systems real time monitoring of user activities and transactions for anomalies.', 2, ''),
(125, 15, 5, 'Verify the application protects against information disclosure attacks, such as direct object reference, tampering, session brute force or other attacks.', 2, ''),
(126, 15, 6, 'Verify the application has sufficient detection and governor controls to protect against brute force (such as continuously using a particular function) or denial of service attacks.', 2, ''),
(127, 15, 7, 'Verify the application has sufficient access controls to prevent elevation of privilege attacks, such as allowing anonymous users from accessing secured data or secured functions, or allowing users to access each other''s details or using privileged functions.', 2, ''),
(128, 15, 8, 'Verify the application will only process business logic flows in sequential step order, with all steps being processed in realistic human time, and not process out of order, skipped steps, process steps from another user, or too quickly submitted transactions.', 2, ''),
(129, 15, 9, 'Verify the application has additional authorization (such as step up or adaptive authentication) for lower value systems, and / or segregation of duties for high value applications to enforce anti-fraud controls as per the risk of application and past fraud.', 2, ''),
(130, 15, 10, 'Verify the application has business limits and enforces them in a trusted location (as on a protected server) on a per user, per day or daily basis, with configurable alerting and automated reactions to automated or unusual attack. Examples include (but not limited to): ensuring new SIM users donâ€™t exceed $10 per day for a new phone account, a forum allowing more than 100 new users per day or preventing posts or private messages until the account has been verified, a health system should not allow a single doctor to access more patient records than they can reasonably treat in a day, or a small business finance system allowing more than 20 invoice payments or $1000 per day across all users. In all cases, the business limits and totals should be reasonable for the business concerned. The only unreasonable outcome is if there are no business limits, alerting or enforcement.', 2, ''),
(131, 16, 1, 'Verify that URL redirects and forwards do not include unvalidated data.', 1, ''),
(132, 16, 2, 'Verify that file names and path data obtained from untrusted sources is canonicalized to eliminate path traversal attacks.', 1, ''),
(133, 16, 3, 'Verify that files obtained from untrusted sources are scanned by antivirus scanners to prevent upload of known malicious content.', 1, ''),
(134, 16, 4, 'Verify that parameters obtained from untrusted sources are not used in manipulating filenames, pathnames or any file system object without first being canonicalized and input validated to prevent local file inclusion attacks.', 1, ''),
(135, 16, 5, 'Verify that parameters obtained from untrusted sources are canonicalized, input validated, and output encoded to prevent remote file inclusion attacks, particularly where input could be executed, such as header, source, or template inclusion.', 1, ''),
(136, 16, 6, 'Verify remote IFRAMEs and HTML5 cross-domain resource sharing does not allow inclusion of arbitrary remote content.', 1, ''),
(137, 16, 7, 'Verify that files obtained from untrusted sources are stored outside the webroot.', 2, ''),
(138, 16, 8, 'Verify that web or application server is configured by default to deny access to remote resources or systems outside the web or application server.', 2, ''),
(139, 16, 9, 'Verify the application code does not execute uploaded data obtained from untrusted sources.', 2, ''),
(140, 16, 10, 'Verify if Flash, Silverlight or other rich internet application (RIA) cross domain resource sharing configuration is configured to prevent unauthenticated or unauthorized remote access.', 2, ''),
(141, 17, 1, 'Verify that the client validates SSL certificates.', 1, ''),
(142, 17, 2, 'Verify that unique device ID (UDID) values are not used as security controls.', 1, ''),
(143, 17, 3, 'Verify that the mobile app does not store sensitive data onto shared resources on the device (e.g. SD card or shared folders)', 1, ''),
(144, 17, 4, 'Verify that sensitive data is not stored in SQLite database on the device.', 1, ''),
(145, 17, 5, 'Verify that secret keys or passwords are not hard-coded in the executable.', 2, ''),
(146, 17, 6, 'Verify that the mobile app prevents leaking of sensitive data via autosnapshot feature of iOS.', 2, ''),
(147, 17, 7, 'Verify that the app cannot be run on a jailbroken or rooted device.', 2, ''),
(148, 17, 8, 'Verify that the session timeout is of a reasonable value.', 2, ''),
(149, 17, 9, 'Verify the permissions being requested as well as the resources that it is authorized to access (i.e. AndroidManifest.xml, iOS Entitlements) .', 2, ''),
(150, 17, 10, 'Verify that crash logs do not contain sensitive data.', 2, ''),
(151, 17, 11, 'Verify that the application binary has been obfuscated.', 3, ''),
(152, 17, 12, 'Verify that all test data has been removed from the app container (.ipa, .apk, .bar).', 2, ''),
(153, 17, 13, 'Verify that the application does not log sensitive data to the system log or filesystem.', 2, ''),
(154, 17, 14, 'Verify that the application does not enable autocomplete for sensitive text input fields, such as passwords, personal information or credit cards.', 2, ''),
(155, 17, 15, 'Verify that the mobile app implements certificate pinning to prevent the proxying of app traffic.', 3, ''),
(156, 17, 16, 'Verify no misconfigurations are present in the configuration files (Debugging flags set, world readable/writable permissions) and that, by default, configuration settings are set to their safest/most secure value.', 3, ''),
(157, 17, 17, 'Verify any 3rd-party libraries in use are up to date, contain no known vulnerabilities.', 3, ''),
(158, 17, 18, 'Verify that web data, such as HTTPS traffic, is not cached.', 3, ''),
(159, 17, 19, 'Verify that the query string is not used for sensitive data. Instead, a POST request via SSL should be used with a CSRF token.', 3, ''),
(160, 17, 20, 'Verify that, if applicable, any personal account numbers are truncated prior to storing on the device.', 3, ''),
(161, 17, 21, 'Verify that the application makes use of Address Space Layout Randomization (ASLR).', 3, ''),
(162, 17, 22, 'Verify that data logged via the keyboard (iOS) does not contain credentials, financial information or other sensitive data.', 3, ''),
(163, 17, 23, 'If an Android app, verify that the app does not create files with permissions of MODE_WORLD_READABLE or MODE_WORLD_WRITABLE.', 3, ''),
(164, 17, 24, 'Verify that sensitive data is stored in a cryptographically secure manner (even when stored in the iOS keychain).', 3, ''),
(165, 17, 25, 'Verify that anti-debugging and reverse engineering mechanisms are implemented in the app.', 3, ''),
(166, 17, 26, 'Verify that the app does not export sensitive activities, intents, content providers etc. on Android.', 3, ''),
(167, 17, 27, 'Verify that mutable structures have been used for sensitive strings such as account numbers and are overwritten when not used. (Mitigate damage from memory analysis attacks).', 3, ''),
(168, 17, 28, 'Verify that any exposed intents, content providers and broadcast receivers perform full data validation on input (Android).', 3, '');

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
