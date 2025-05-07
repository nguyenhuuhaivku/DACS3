package com.example.restaurant.ui.screens

import androidx.compose.foundation.layout.Arrangement
import androidx.compose.foundation.layout.Box
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.Spacer
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.height
import androidx.compose.foundation.layout.padding
import androidx.compose.foundation.layout.size
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.text.KeyboardOptions
import androidx.compose.foundation.verticalScroll
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.AccountCircle
import androidx.compose.material.icons.filled.ArrowBack
import androidx.compose.material.icons.filled.Email
import androidx.compose.material.icons.filled.Lock
import androidx.compose.material.icons.filled.Phone
import androidx.compose.material.icons.filled.Visibility
import androidx.compose.material.icons.filled.VisibilityOff
import androidx.compose.material3.Button
import androidx.compose.material3.CircularProgressIndicator
import androidx.compose.material3.ExperimentalMaterial3Api
import androidx.compose.material3.Icon
import androidx.compose.material3.IconButton
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.OutlinedTextField
import androidx.compose.material3.Scaffold
import androidx.compose.material3.SnackbarHost
import androidx.compose.material3.SnackbarHostState
import androidx.compose.material3.Text
import androidx.compose.material3.TextButton
import androidx.compose.runtime.Composable
import androidx.compose.runtime.LaunchedEffect
import androidx.compose.runtime.getValue
import androidx.compose.runtime.livedata.observeAsState
import androidx.compose.runtime.mutableStateOf
import androidx.compose.runtime.remember
import androidx.compose.runtime.rememberCoroutineScope
import androidx.compose.runtime.setValue
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.res.stringResource
import androidx.compose.ui.text.input.ImeAction
import androidx.compose.ui.text.input.KeyboardType
import androidx.compose.ui.text.input.PasswordVisualTransformation
import androidx.compose.ui.text.input.VisualTransformation
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.unit.dp
import androidx.lifecycle.viewmodel.compose.viewModel
import com.example.restaurant.R
import com.example.restaurant.ui.viewmodel.AuthViewModel
import kotlinx.coroutines.delay
import kotlinx.coroutines.launch

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun RegisterScreen(
    viewModel: AuthViewModel = viewModel(),
    onNavigateToLogin: () -> Unit,
    onRegisterSuccess: () -> Unit,
    onBackClick: () -> Unit = onNavigateToLogin
) {
    var name by remember { mutableStateOf("") }
    var email by remember { mutableStateOf("") }
    var phone by remember { mutableStateOf("") }
    var password by remember { mutableStateOf("") }
    var confirmPassword by remember { mutableStateOf("") }
    var passwordVisible by remember { mutableStateOf(false) }
    var confirmPasswordVisible by remember { mutableStateOf(false) }
    
    val isLoading by viewModel.isLoading.observeAsState(false)
    val error by viewModel.errorMessage.observeAsState(null)
    val successMessage by viewModel.successMessage.observeAsState(null)
    val isLoggedIn by viewModel.isLoggedIn.observeAsState(false)
    
    val snackbarHostState = remember { SnackbarHostState() }
    val scope = rememberCoroutineScope()
    val scrollState = rememberScrollState()
    
    // Form validation states
    var nameError by remember { mutableStateOf<String?>(null) }
    var emailError by remember { mutableStateOf<String?>(null) }
    var phoneError by remember { mutableStateOf<String?>(null) }
    var passwordError by remember { mutableStateOf<String?>(null) }
    var confirmPasswordError by remember { mutableStateOf<String?>(null) }
    var registerAttempted = remember { mutableStateOf(false) }
    
    // Observe registration success via successMessage
    LaunchedEffect(successMessage) {
        successMessage?.let {
            scope.launch {
                snackbarHostState.showSnackbar(it)
            }
            viewModel.clearSuccessMessage()
            
            // Once the success message is received, complete the registration flow
            if (registerAttempted.value) {
                // Forceful refresh
                viewModel.refreshUserData()
                
                // Give a brief delay to allow data refresh
                delay(200)
                
                // Navigate away even if isLoggedIn is not detected yet
                onRegisterSuccess()
                registerAttempted.value = false
            }
        }
    }
    
    // Kiểm tra trạng thái đăng nhập trực tiếp từ repository
    LaunchedEffect(Unit) {
        if (viewModel.repository.isLoggedIn()) {
            // Nếu đã đăng nhập, cập nhật trạng thái và điều hướng
            viewModel.refreshUserData()
            delay(100)
            onRegisterSuccess()
        }
    }
    
    // Observe errors
    LaunchedEffect(error) {
        error?.let {
            scope.launch {
                snackbarHostState.showSnackbar(it)
            }
            registerAttempted.value = false
        }
    }
    
    Scaffold(
        snackbarHost = { SnackbarHost(snackbarHostState) }
    ) { paddingValues ->
        Box(
            modifier = Modifier
                .fillMaxSize()
                .padding(paddingValues)
        ) {
            IconButton(
                onClick = onBackClick,
                modifier = Modifier
                    .align(Alignment.TopStart)
                    .padding(16.dp)
            ) {
                Icon(
                    imageVector = Icons.Default.ArrowBack,
                    contentDescription = "Back"
                )
            }
            
            Column(
                modifier = Modifier
                    .fillMaxSize()
                    .padding(16.dp)
                    .verticalScroll(scrollState),
                horizontalAlignment = Alignment.CenterHorizontally,
                verticalArrangement = Arrangement.Center
            ) {
                Text(
                    text = stringResource(R.string.create_account),
                    style = MaterialTheme.typography.headlineMedium,
                    textAlign = TextAlign.Center
                )
                
                Spacer(modifier = Modifier.height(8.dp))
                
                Text(
                    text = stringResource(R.string.register_message),
                    style = MaterialTheme.typography.bodyLarge,
                    textAlign = TextAlign.Center
                )
                
                Spacer(modifier = Modifier.height(32.dp))
                
                // Name field
                OutlinedTextField(
                    value = name,
                    onValueChange = { 
                        name = it
                        nameError = null
                    },
                    label = { Text(stringResource(R.string.name)) },
                    leadingIcon = { Icon(Icons.Default.AccountCircle, contentDescription = null) },
                    isError = nameError != null,
                    supportingText = { nameError?.let { error -> Text(error) } },
                    modifier = Modifier.fillMaxWidth(),
                    singleLine = true,
                    keyboardOptions = KeyboardOptions(imeAction = ImeAction.Next)
                )
                
                Spacer(modifier = Modifier.height(16.dp))
                
                // Email field
                OutlinedTextField(
                    value = email,
                    onValueChange = { 
                        email = it
                        emailError = null
                    },
                    label = { Text(stringResource(R.string.email)) },
                    leadingIcon = { Icon(Icons.Default.Email, contentDescription = null) },
                    isError = emailError != null,
                    supportingText = { emailError?.let { error -> Text(error) } },
                    modifier = Modifier.fillMaxWidth(),
                    singleLine = true,
                    keyboardOptions = KeyboardOptions(
                        keyboardType = KeyboardType.Email,
                        imeAction = ImeAction.Next
                    )
                )
                
                Spacer(modifier = Modifier.height(16.dp))
                
                // Phone field
                OutlinedTextField(
                    value = phone,
                    onValueChange = { 
                        phone = it
                        phoneError = null
                    },
                    label = { Text(stringResource(R.string.phone)) },
                    leadingIcon = { Icon(Icons.Default.Phone, contentDescription = null) },
                    isError = phoneError != null,
                    supportingText = { phoneError?.let { error -> Text(error) } },
                    modifier = Modifier.fillMaxWidth(),
                    singleLine = true,
                    keyboardOptions = KeyboardOptions(
                        keyboardType = KeyboardType.Phone,
                        imeAction = ImeAction.Next
                    )
                )
                
                Spacer(modifier = Modifier.height(16.dp))
                
                // Password field
                OutlinedTextField(
                    value = password,
                    onValueChange = { 
                        password = it
                        passwordError = null
                    },
                    label = { Text(stringResource(R.string.password)) },
                    leadingIcon = { Icon(Icons.Default.Lock, contentDescription = null) },
                    trailingIcon = {
                        IconButton(onClick = { passwordVisible = !passwordVisible }) {
                            Icon(
                                if (passwordVisible) Icons.Default.VisibilityOff else Icons.Default.Visibility,
                                contentDescription = if (passwordVisible) "Hide password" else "Show password"
                            )
                        }
                    },
                    visualTransformation = if (passwordVisible) VisualTransformation.None else PasswordVisualTransformation(),
                    isError = passwordError != null,
                    supportingText = { passwordError?.let { error -> Text(error) } },
                    modifier = Modifier.fillMaxWidth(),
                    singleLine = true,
                    keyboardOptions = KeyboardOptions(
                        keyboardType = KeyboardType.Password,
                        imeAction = ImeAction.Next
                    )
                )
                
                Spacer(modifier = Modifier.height(16.dp))
                
                // Confirm Password field
                OutlinedTextField(
                    value = confirmPassword,
                    onValueChange = { 
                        confirmPassword = it
                        confirmPasswordError = null
                    },
                    label = { Text(stringResource(R.string.confirm_password)) },
                    leadingIcon = { Icon(Icons.Default.Lock, contentDescription = null) },
                    trailingIcon = {
                        IconButton(onClick = { confirmPasswordVisible = !confirmPasswordVisible }) {
                            Icon(
                                if (confirmPasswordVisible) Icons.Default.VisibilityOff else Icons.Default.Visibility,
                                contentDescription = if (confirmPasswordVisible) "Hide confirm password" else "Show confirm password"
                            )
                        }
                    },
                    visualTransformation = if (confirmPasswordVisible) VisualTransformation.None else PasswordVisualTransformation(),
                    isError = confirmPasswordError != null,
                    supportingText = { confirmPasswordError?.let { error -> Text(error) } },
                    modifier = Modifier.fillMaxWidth(),
                    singleLine = true,
                    keyboardOptions = KeyboardOptions(
                        keyboardType = KeyboardType.Password,
                        imeAction = ImeAction.Done
                    )
                )
                
                Spacer(modifier = Modifier.height(32.dp))
                
                // Register button
                Button(
                    onClick = {
                        // Validate form
                        val isNameValid = validateName(name) { error ->
                            nameError = error
                        }
                        
                        val isEmailValid = validateEmail(email) { error ->
                            emailError = error
                        }
                        
                        val isPhoneValid = validatePhone(phone) { error ->
                            phoneError = error
                        }
                        
                        val isPasswordValid = validatePassword(password) { error ->
                            passwordError = error
                        }
                        
                        val isConfirmPasswordValid = validateConfirmPassword(password, confirmPassword) { error ->
                            confirmPasswordError = error
                        }
                        
                        // Submit if valid
                        if (isNameValid && isEmailValid && isPhoneValid && isPasswordValid && isConfirmPasswordValid) {
                            registerAttempted.value = true
                            
                            // Xử lý cực kỳ nhanh cho UI
                            scope.launch {
                                viewModel.register(name, email, password, confirmPassword)
                                
                                // Cho thời gian để repository cập nhật
                                delay(300)
                                
                                // Kiểm tra nếu đăng ký thành công
                                if (viewModel.repository.isLoggedIn()) {
                                    // Đảm bảo dữ liệu người dùng được cập nhật
                                    viewModel.refreshUserData()
                                    
                                    // Điều hướng người dùng
                                    onRegisterSuccess()
                                }
                            }
                        }
                    },
                    modifier = Modifier.fillMaxWidth(),
                    enabled = !isLoading
                ) {
                    if (isLoading) {
                        CircularProgressIndicator(
                            modifier = Modifier.size(24.dp),
                            color = MaterialTheme.colorScheme.onPrimary,
                            strokeWidth = 2.dp
                        )
                    } else {
                        Text(stringResource(R.string.register))
                    }
                }
                
                Spacer(modifier = Modifier.height(16.dp))
                
                // Login link
                TextButton(
                    onClick = onNavigateToLogin
                ) {
                    Text(stringResource(R.string.have_account) + " " + stringResource(R.string.login))
                }
            }
        }
    }
}

private fun validateName(name: String, setError: (String) -> Unit): Boolean {
    return when {
        name.isEmpty() -> {
            setError("Tên không được để trống")
            false
        }
        name.length < 2 -> {
            setError("Tên phải có ít nhất 2 ký tự")
            false
        }
        else -> true
    }
}

private fun validateEmail(email: String, setError: (String) -> Unit): Boolean {
    return when {
        email.isEmpty() -> {
            setError("Email không được để trống")
            false
        }
        !android.util.Patterns.EMAIL_ADDRESS.matcher(email).matches() -> {
            setError("Email không hợp lệ")
            false
        }
        else -> true
    }
}

private fun validatePhone(phone: String, setError: (String) -> Unit): Boolean {
    return when {
        phone.isEmpty() -> {
            setError("Số điện thoại không được để trống")
            false
        }
        !android.util.Patterns.PHONE.matcher(phone).matches() -> {
            setError("Số điện thoại không hợp lệ")
            false
        }
        else -> true
    }
}

private fun validatePassword(password: String, setError: (String) -> Unit): Boolean {
    return when {
        password.isEmpty() -> {
            setError("Mật khẩu không được để trống")
            false
        }
        password.length < 6 -> {
            setError("Mật khẩu phải có ít nhất 6 ký tự")
            false
        }
        else -> true
    }
}

private fun validateConfirmPassword(password: String, confirmPassword: String, setError: (String) -> Unit): Boolean {
    return when {
        confirmPassword.isEmpty() -> {
            setError("Vui lòng xác nhận mật khẩu")
            false
        }
        confirmPassword != password -> {
            setError("Mật khẩu xác nhận không khớp")
            false
        }
        else -> true
    }
} 