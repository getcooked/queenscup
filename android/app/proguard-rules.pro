# Keep the serializable API models and their generated serializers.
-keepclassmembers class ph.queenscup.customer.data.model.** {
    *** Companion;
}
-keepclasseswithmembers class ph.queenscup.customer.data.model.** {
    kotlinx.serialization.KSerializer serializer(...);
}
