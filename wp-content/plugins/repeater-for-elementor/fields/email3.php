<?php

use Elementor\Controls_Manager;
use Elementor\Core\Admin\Admin_Notices;
use ElementorPro\Core\Utils\Hints;
use ElementorPro\Core\Utils;
use ElementorPro\Core\Utils\Collection;
use ElementorPro\Modules\Forms\Classes\Ajax_Handler;
use ElementorPro\Modules\Forms\Classes\Action_Base;
use ElementorPro\Modules\Forms\Classes\Form_Record;
use ElementorPro\Modules\Forms\Fields\Upload;
use ElementorPro\Modules\Forms\Actions\Email;

if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Yeekit_El_Email3 extends Email
{

    public function get_name()
    {
        return 'yeekit_email_repeater';
    }

    public function get_label()
    {
        return esc_html__('Email Repeater', 'repeater-for-elementor');
    }

    protected function get_control_id($control_id)
    {
        return $control_id . '_3_yeekit_email_repeater';
    }

    protected function get_reply_to($record, $fields)
    {
        return isset($fields['email_reply_to']) ? $fields['email_reply_to'] : '';
    }

    public function register_settings_section($widget)
    {
        parent::register_settings_section($widget);

        $admin_email = get_option('admin_email');

        $widget->update_control(
            $this->get_control_id('email_reply_to'),
            [
                'type' => Controls_Manager::TEXT,
                'default' => $admin_email,
                'placeholder' => $admin_email,
                'ai' => [
                    'active' => false,
                ],
            ]
        );

        $widget->update_control(
            $this->get_control_id('form_metadata'),
            [
                'default' => [],
            ]
        );
    }
    public function run($record, $ajax_handler)
    {
        $settings = $record->get('form_settings');
        $send_html = 'plain' !== $settings[$this->get_control_id('email_content_type')];
        $line_break = $send_html ? '<br>' : "\n";

        $fields = [
            'email_to' => get_option('admin_email'),
            /* translators: %s: Site title. */
            'email_subject' => sprintf(esc_html__('New message from "%s"', 'elementor-pro'), get_bloginfo('name')), //phpcs:ignore WordPress.WP.I18n.TextDomainMismatch
            'email_content' => '[all-fields]',
            'email_from_name' => get_bloginfo('name'),
            'email_from' => get_bloginfo('admin_email'),
            'email_reply_to' => 'noreply@' . Utils::get_site_domain(),
            'email_to_cc' => '',
            'email_to_bcc' => '',
        ];

        foreach ($fields as $key => $default) {
            if ($key == "email_to") {
                $setting = trim($settings[$this->get_control_id($key)]);
                $setting = $record->replace_setting_shortcodes($setting);
                $setting = $this->get_emails_from_string($setting);
            } else {
                $setting = trim($settings[$this->get_control_id($key)]);
                $setting = $record->replace_setting_shortcodes($setting);
            }

            if (! empty($setting)) {
                $fields[$key] = $setting;
            }
        }



        $email_reply_to = $this->get_reply_to($record, $fields);

        $fields['email_content'] = $this->replace_content_shortcodes($fields['email_content'], $record, $line_break);

        $email_meta = '';

        $form_metadata_settings = $settings[$this->get_control_id('form_metadata')];

        foreach ($record->get('meta') as $id => $field) {
            if (in_array($id, $form_metadata_settings)) {
                $email_meta .= $this->field_formatted($field) . $line_break;
            }
        }

        if (! empty($email_meta)) {
            $fields['email_content'] .= $line_break . '---' . $line_break . $line_break . $email_meta;
        }

        $headers = sprintf('From: %s <%s>' . "\r\n", $fields['email_from_name'], $fields['email_from']);
        $headers .= sprintf('Reply-To: %s' . "\r\n", $email_reply_to);

        if ($send_html) {
            $headers .= 'Content-Type: text/html; charset=UTF-8' . "\r\n";
        }

        $cc_header = '';
        if (! empty($fields['email_to_cc'])) {
            $cc_header = 'Cc: ' . $fields['email_to_cc'] . "\r\n";
        }

        /**
         * Email headers.
         *
         * Filters the headers sent when an email is sent from Elementor forms. This
         * hook allows developers to alter email headers triggered by Elementor forms.
         *
         * @since 1.0.0
         *
         * @param string|array $headers Additional headers.
         */
        $headers = apply_filters('elementor_pro/forms/wp_mail_headers', $headers);

        /**
         * Email content.
         *
         * Filters the content of the email sent by Elementor forms. This hook allows
         * developers to alter the content of the email sent by Elementor forms.
         *
         * @since 1.0.0
         *
         * @param string $email_content Email content.
         */
        $fields['email_content'] = apply_filters('elementor_pro/forms/wp_mail_message', $fields['email_content']);

        $attachments_mode_attach = $this->get_file_by_attachment_type($settings['form_fields'], $record, Upload::MODE_ATTACH);
        $attachments_mode_both = $this->get_file_by_attachment_type($settings['form_fields'], $record, Upload::MODE_BOTH);

        $email_sent = wp_mail(
            $fields['email_to'],
            $fields['email_subject'],
            $fields['email_content'],
            $headers . $cc_header,
            array_merge($attachments_mode_attach, $attachments_mode_both)
        );

        if (! empty($fields['email_to_bcc'])) {
            $bcc_emails = explode(',', $fields['email_to_bcc']);
            foreach ($bcc_emails as $bcc_email) {
                wp_mail(
                    trim($bcc_email),
                    $fields['email_subject'],
                    $fields['email_content'],
                    $headers,
                    array_merge($attachments_mode_attach, $attachments_mode_both)
                );
            }
        }

        foreach ($attachments_mode_attach as $file) {
            @unlink($file);
        }

        /**
         * Elementor form mail sent.
         *
         * Fires when an email was sent successfully by Elementor forms. This
         * hook allows developers to add functionality after mail sending.
         *
         * @since 1.0.0
         *
         * @param array       $settings Form settings.
         * @param Form_Record $record   An instance of the form record.
         */
        do_action('elementor_pro/forms/mail_sent', $settings, $record);

        if (! $email_sent) {
            $message = Ajax_Handler::get_default_message(Ajax_Handler::SERVER_ERROR, $settings);

            $ajax_handler->add_error_message($message);

            throw new \Exception($message); //phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
        }
    }
    public function get_emails_from_string($text)
    {
        $pattern = '/[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}/i';

        if (preg_match_all($pattern, $text, $matches)) {
            return array_unique($matches[0]);
        }

        return [];
    }
    private function replace_content_shortcodes($email_content, $record, $line_break)
    {
        $email_content = do_shortcode($email_content);
        $all_fields_shortcode = '[all-fields]';

        if (false !== strpos($email_content, $all_fields_shortcode)) {
            $text = '';
            foreach ($record->get('fields') as $field) {
                // Skip upload fields that only attached to the email
                if (isset($field['attachment_type']) && Upload::MODE_ATTACH === $field['attachment_type']) {
                    continue;
                }

                $formatted = $this->field_formatted($field);
                if (('textarea' === $field['type']) && ('<br>' === $line_break)) {
                    $formatted = str_replace(["\r\n", "\n", "\r"], '<br />', $formatted);
                }

                $text .= $formatted . $line_break;
            }

            $email_content = str_replace($all_fields_shortcode, $text, $email_content);
        }

        return $email_content;
    }
    private function field_formatted($field)
    {
        $formatted = '';
        if (! empty($field['title'])) {
            $formatted = sprintf('%s: %s', $field['title'], $field['value']);
        } elseif (! empty($field['value'])) {
            $formatted = sprintf('%s', $field['value']);
        }

        return $formatted;
    }
    private function get_file_by_attachment_type($form_fields, $record, $type)
    {
        return Collection::make($form_fields)
            ->filter(function ($field) use ($type) {
                return $type === $field['attachment_type'];
            })
            ->map(function ($field) use ($record) {
                $id = $field['custom_id'];

                return $record->get('files')[$id]['path'] ?? null;
            })
            ->filter()
            ->flatten()
            ->values();
    }
}
