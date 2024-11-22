/**
 * Update user data in web storage.
 *
 * @since 5.7.0
 * @param {Object} data - The user data JSON.
 */

function fcn_setUserData(data) {
  localStorage.setItem('fcnUserData', JSON.stringify(data));
}
