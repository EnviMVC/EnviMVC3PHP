        // _____scaffold_form_name_____が既に使用されていないかどうかチェックする
        $_____model_pascal_case_name_____  = _____model_pascal_case_name_____Peer::_____scaffold_pascal_case_name_____Check($input_data['_____scaffold_name_____']);
        if ($_____model_pascal_case_name_____ instanceof _____model_pascal_case_name_____) {
            EnviRequest::setError('_____scaffold_name______ck', '_____scaffold_name_____', 0, _('既に使用されている_____scaffold_form_name_____です。'));
            return Envi::ERROR;
        }

