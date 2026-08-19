import java.util.Properties

plugins {
    id("com.android.application")
    id("org.jetbrains.kotlin.android")
    id("org.jetbrains.kotlin.plugin.serialization")
}

// Firebase is optional. The google-services plugin hard-fails when its config
// file is missing, so it is applied only once you have added one. The app
// builds and runs without it; push is simply inactive and the Track tab falls
// back to polling.
if (file("google-services.json").exists()) {
    apply(plugin = "com.google.gms.google-services")
}

// Where the app looks for the Laravel API. Set these in gradle.properties (or
// pass -P on the command line) rather than editing this file, so the same
// source builds against a local server or the live site.
val debugApiUrl = (findProperty("QC_API_BASE_URL_DEBUG") as String?)
    ?: "http://10.0.2.2:8000/api/v1/"
val releaseApiUrl = (findProperty("QC_API_BASE_URL_RELEASE") as String?)
    ?: debugApiUrl

android {
    namespace = "ph.queenscup.customer"
    compileSdk = 34

    defaultConfig {
        applicationId = "ph.queenscup.customer"
        minSdk = 24
        targetSdk = 34
        versionCode = 1
        versionName = "1.0"
    }

    // Release signing. The keystore and its passwords stay out of git; set
    // them in gradle.properties (or pass -P) on whichever machine cuts a
    // release. Without them the release build simply stays unsigned.
    signingConfigs {
        create("release") {
            // Passwords come from the uncommitted keystore.properties.
            val secrets = Properties()
            val secretsFile = rootProject.file("keystore.properties")
            if (secretsFile.exists()) secretsFile.inputStream().use { secrets.load(it) }

            val storeFileName = (findProperty("QC_KEYSTORE_FILE") as String?) ?: "queenscup-release.jks"
            val store = rootProject.file(storeFileName)
            if (store.exists()) {
                storeFile = store
                storePassword = secrets.getProperty("QC_KEYSTORE_PASSWORD") ?: ""
                keyAlias = (findProperty("QC_KEY_ALIAS") as String?) ?: "queenscup"
                keyPassword = secrets.getProperty("QC_KEY_PASSWORD") ?: ""
            }
        }
    }

    buildTypes {
        release {
            signingConfig = signingConfigs.getByName("release")
            isMinifyEnabled = false
            proguardFiles(getDefaultProguardFile("proguard-android-optimize.txt"), "proguard-rules.pro")
            buildConfigField("String", "API_BASE_URL", "\"$releaseApiUrl\"")
        }
        debug {
            applicationIdSuffix = ".debug"
            buildConfigField("String", "API_BASE_URL", "\"$debugApiUrl\"")
        }
    }

    compileOptions {
        sourceCompatibility = JavaVersion.VERSION_17
        targetCompatibility = JavaVersion.VERSION_17
    }

    kotlinOptions {
        jvmTarget = "17"
    }

    buildFeatures {
        compose = true
        buildConfig = true
    }

    composeOptions {
        kotlinCompilerExtensionVersion = "1.5.14"
    }

    packaging {
        resources.excludes += "/META-INF/{AL2.0,LGPL2.1}"
    }
}

dependencies {
    // firebase-messaging drags in androidx.fragment 1.0.0 through
    // play-services-base. That predates the ActivityResult API MainActivity
    // uses to ask for notification permission, and lint fails the release
    // build over it. Constrain the version rather than suppressing the check.
    constraints {
        implementation("androidx.fragment:fragment:1.8.2") {
            because("ActivityResult needs Fragment 1.3.0 or newer")
        }
    }

    val composeBom = platform("androidx.compose:compose-bom:2024.06.00")
    implementation(composeBom)

    implementation("androidx.core:core-ktx:1.13.1")
    implementation("androidx.lifecycle:lifecycle-runtime-ktx:2.8.3")
    implementation("androidx.lifecycle:lifecycle-viewmodel-compose:2.8.3")
    // Supplies collectAsStateWithLifecycle, used by every screen.
    implementation("androidx.lifecycle:lifecycle-runtime-compose:2.8.3")
    implementation("androidx.activity:activity-compose:1.9.0")

    implementation("androidx.compose.ui:ui")
    implementation("androidx.compose.ui:ui-graphics")
    implementation("androidx.compose.ui:ui-tooling-preview")
    implementation("androidx.compose.material3:material3")
    implementation("androidx.compose.material:material-icons-extended")
    implementation("androidx.navigation:navigation-compose:2.7.7")

    implementation("androidx.datastore:datastore-preferences:1.1.1")

    implementation("com.squareup.retrofit2:retrofit:2.11.0")
    implementation("com.jakewharton.retrofit:retrofit2-kotlinx-serialization-converter:1.0.0")
    implementation("org.jetbrains.kotlinx:kotlinx-serialization-json:1.6.3")
    implementation("com.squareup.okhttp3:logging-interceptor:4.12.0")

    implementation("io.coil-kt:coil-compose:2.6.0")

    // Push. Remove these together with the google-services plugin above if you
    // are not using Firebase yet; the app falls back to polling.
    implementation(platform("com.google.firebase:firebase-bom:33.1.2"))
    implementation("com.google.firebase:firebase-messaging-ktx")

    debugImplementation("androidx.compose.ui:ui-tooling")
}
